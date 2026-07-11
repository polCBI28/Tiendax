<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function detalle(Request $request)
    {
        $query = Venta::with('cliente', 'user', 'detalles.producto.categoria');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('numero_boleta', 'like', "%{$q}%")
                    ->orWhereHas('cliente', fn ($c) => $c->where('nombre', 'like', "%{$q}%"));
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
        if (! in_array($ordenar, $columnasPermitidas)) {
            $ordenar = 'fecha_venta';
        }

        $ventas = $query->orderBy($ordenar, $dir)->paginate(20)->withQueryString();

        $clientes = Cliente::orderBy('nombre')->get();

        $estadisticas = [
            'total_ventas' => $ventas->total(),
            'ingresos' => (clone $query)->sum('total'),
            'descuentos' => (clone $query)->where('descuento_valor', '>', 0)->sum(
                DB::raw("CASE WHEN descuento_tipo = 'porcentaje' THEN total * descuento_valor / (100 - descuento_valor) ELSE descuento_valor END")
            ),
            'ticket_promedio' => (clone $query)->avg('total') ?? 0,
        ];

        return view('ventas.detalle', compact('ventas', 'clientes', 'estadisticas'));
    }

    public function show(Venta $venta)
    {
        $venta->load('cliente', 'user', 'detalles.producto');

        return view('ventas.show', compact('venta'));
    }

    public function completarPago(Venta $venta)
    {
        $venta->update([
            'adelanto' => $venta->total,
            'estado' => 'completado',
        ]);

        return redirect()->route('ventas.show', $venta)->with('success', 'Pago completado correctamente.');
    }
}
