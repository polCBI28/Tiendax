<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\DetalleVenta;
use App\Models\Movimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::with('cliente', 'user')->paginate(10);
        return view('ventas.index', compact('ventas'));
    }

public function create()
{
    $clientes   = Cliente::all();
    $categorias = \App\Models\Categoria::where('activo', true)->get();
    $productos  = Producto::where('activo', true)
                    ->where('stock', '>', 0)
                    ->with('categoria')
                    ->get();

    return view('ventas.create', compact('clientes', 'categorias', 'productos'));
}

    public function store(Request $request)
    {
        $request->validate([
            'fecha_venta'      => 'required|date',
            'productos'        => 'required|array|min:1',
            'descuento_tipo'   => 'nullable|in:monto,porcentaje',
            'descuento_valor'  => 'nullable|numeric|min:0',
            'adelanto'         => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $subtotal = 0;
            foreach ($request->productos as $item) {
                $subtotal += $item['precio_unitario'] * $item['cantidad'];
            }

            $descuentoTipo  = $request->descuento_tipo;
            $descuentoValor = (float) ($request->descuento_valor ?? 0);
            $descuento = 0;

            if ($descuentoValor > 0 && $descuentoTipo) {
                if ($descuentoTipo === 'porcentaje') {
                    $descuentoValor = min($descuentoValor, 100);
                    $descuento = round($subtotal * $descuentoValor / 100, 2);
                } else {
                    $descuento = min($descuentoValor, $subtotal);
                }
            }

            $total = $subtotal - $descuento;

            $adelanto = (float) ($request->adelanto ?? 0);
            $adelanto = min($adelanto, $total);

            $estado = $request->estado ?? 'completado';
            if ($estado === 'completado' && $adelanto > 0 && $adelanto < $total) {
                $estado = 'pendiente';
            }

            $venta = Venta::create([
                'cliente_id'      => $request->cliente_id,
                'user_id'         => auth()->id(),
                'numero_boleta'   => 'B001-' . str_pad((Venta::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT),
                'fecha_venta'     => $request->fecha_venta,
                'total'           => $total,
                'adelanto'        => $adelanto,
                'descuento_tipo'  => $descuentoValor > 0 ? $descuentoTipo : null,
                'descuento_valor' => $descuentoValor > 0 ? $descuentoValor : 0,
                'estado'          => $estado,
            ]);

            foreach ($request->productos as $item) {
                DetalleVenta::create([
                    'venta_id'       => $venta->id,
                    'producto_id'    => $item['producto_id'],
                    'cantidad'       => $item['cantidad'],
                    'precio_unitario'=> $item['precio_unitario'],
                    'subtotal'       => $item['precio_unitario'] * $item['cantidad'],
                ]);

                // Descontar stock
                $producto = Producto::find($item['producto_id']);
                $producto->decrement('stock', $item['cantidad']);

                // Registrar movimiento
                Movimiento::create([
                    'producto_id' => $item['producto_id'],
                    'user_id'     => auth()->id(),
                    'tipo'        => 'salida',
                    'cantidad'    => $item['cantidad'],
                    'motivo'      => 'Venta ' . $venta->numero_boleta,
                ]);
            }
        });

        return redirect()->route('ventas.index')->with('success', 'Venta registrada correctamente.');
    }

    public function detalle(Request $request)
    {
        $query = Venta::with('cliente', 'user', 'detalles.producto.categoria');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('numero_boleta', 'like', "%{$q}%")
                  ->orWhereHas('cliente', fn($c) => $c->where('nombre', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        if ($request->filled('desde')) {
            $query->whereDate('fecha_venta', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('fecha_venta', '<=', $request->hasta);
        }

        if ($request->filled('descuento')) {
            if ($request->descuento === 'con') {
                $query->where('descuento_valor', '>', 0);
            } else {
                $query->where(function ($w) {
                    $w->where('descuento_valor', 0)->orWhereNull('descuento_tipo');
                });
            }
        }

        $ordenar = $request->get('ordenar', 'fecha_venta');
        $dir = $request->get('dir', 'desc');
        $columnasPermitidas = ['fecha_venta', 'total', 'numero_boleta', 'estado', 'created_at'];
        if (!in_array($ordenar, $columnasPermitidas)) $ordenar = 'fecha_venta';

        $ventas = $query->orderBy($ordenar, $dir)->paginate(20)->withQueryString();

        $clientes = Cliente::orderBy('nombre')->get();

        $totalesQuery = (clone $query)->getQuery();
        $estadisticas = [
            'total_ventas'     => $ventas->total(),
            'ingresos'         => (clone $query)->sum('total'),
            'descuentos'       => (clone $query)->where('descuento_valor', '>', 0)->sum(
                DB::raw("CASE WHEN descuento_tipo = 'porcentaje' THEN total * descuento_valor / (100 - descuento_valor) ELSE descuento_valor END")
            ),
            'ticket_promedio'  => (clone $query)->avg('total') ?? 0,
        ];

        return view('ventas.detalle', compact('ventas', 'clientes', 'estadisticas'));
    }

    public function show(Venta $venta)
    {
        $venta->load('cliente', 'user', 'detalles.producto');
        return view('ventas.show', compact('venta'));
    }

    public function edit(Venta $venta)
    {
        return view('ventas.edit', compact('venta'));
    }

    public function update(Request $request, Venta $venta)
    {
        $venta->update(['estado' => $request->estado]);
        return redirect()->route('ventas.index')->with('success', 'Venta actualizada correctamente.');
    }

    public function completarPago(Venta $venta)
    {
        $venta->update([
            'adelanto' => $venta->total,
            'estado'   => 'completado',
        ]);

        return redirect()->route('ventas.show', $venta)->with('success', 'Pago completado correctamente.');
    }

    public function destroy(Venta $venta)
    {
        $venta->delete();
        return redirect()->route('ventas.index')->with('success', 'Venta eliminada correctamente.');
    }
}