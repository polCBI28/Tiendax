<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Movimiento;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovimientoController extends Controller
{
    public function index()
    {
        $resumenDiario = Venta::select(
                DB::raw('DATE(fecha_venta) as fecha'),
                DB::raw('COUNT(*) as num_ventas'),
                DB::raw('SUM(total) as total_dia')
            )
            ->where('estado', '!=', 'cancelado')
            ->groupBy('fecha')
            ->orderByDesc('fecha')
            ->paginate(15);

        $fechas = $resumenDiario->pluck('fecha')->toArray();

        $unidadesPorDia = DetalleVenta::join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->whereIn(DB::raw('DATE(ventas.fecha_venta)'), $fechas)
            ->where('ventas.estado', '!=', 'cancelado')
            ->select(
                DB::raw('DATE(ventas.fecha_venta) as fecha'),
                DB::raw('SUM(detalle_ventas.cantidad) as unidades')
            )
            ->groupBy('fecha')
            ->pluck('unidades', 'fecha');

        $categoriasPorDia = DetalleVenta::join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->join('productos', 'productos.id', '=', 'detalle_ventas.producto_id')
            ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->whereIn(DB::raw('DATE(ventas.fecha_venta)'), $fechas)
            ->where('ventas.estado', '!=', 'cancelado')
            ->select(
                DB::raw('DATE(ventas.fecha_venta) as fecha'),
                'categorias.nombre as categoria',
                'categorias.icono',
                DB::raw('SUM(detalle_ventas.subtotal) as total_cat')
            )
            ->groupBy('fecha', 'categorias.nombre', 'categorias.icono')
            ->orderByDesc('total_cat')
            ->get()
            ->groupBy('fecha');

        $movimientosTipoPorDia = Movimiento::whereIn('fecha', $fechas)
            ->select(
                'fecha',
                'tipo',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(cantidad) as unidades')
            )
            ->groupBy('fecha', 'tipo')
            ->get()
            ->groupBy(fn($m) => $m->fecha->toDateString())
            ->map(fn($items) => $items->keyBy('tipo'));

        $ajustesPorDia = Movimiento::where(function ($q) {
                $q->where('motivo', 'not like', 'Venta %')->orWhereNull('motivo');
            })
            ->select(
                'fecha',
                DB::raw('COUNT(*) as total_ajustes')
            )
            ->groupBy('fecha')
            ->pluck('total_ajustes', 'fecha');

        return view('movimientos.index', compact(
            'resumenDiario', 'unidadesPorDia', 'categoriasPorDia', 'movimientosTipoPorDia', 'ajustesPorDia'
        ));
    }

    public function show(string $fecha)
    {
        $fechaCarbon = \Carbon\Carbon::parse($fecha);

        $porCategoria = DetalleVenta::join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->join('productos', 'productos.id', '=', 'detalle_ventas.producto_id')
            ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->whereDate('ventas.fecha_venta', $fecha)
            ->where('ventas.estado', '!=', 'cancelado')
            ->select(
                'categorias.id as categoria_id',
                'categorias.nombre as categoria',
                'categorias.icono',
                DB::raw('COUNT(DISTINCT ventas.id) as num_ventas'),
                DB::raw('SUM(detalle_ventas.cantidad) as unidades'),
                DB::raw('SUM(detalle_ventas.subtotal) as total')
            )
            ->groupBy('categorias.id', 'categorias.nombre', 'categorias.icono')
            ->orderByDesc('total')
            ->get();

        $totalDia = $porCategoria->sum('total');

        $productosPorCategoria = DetalleVenta::join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->join('productos', 'productos.id', '=', 'detalle_ventas.producto_id')
            ->whereDate('ventas.fecha_venta', $fecha)
            ->where('ventas.estado', '!=', 'cancelado')
            ->select(
                'productos.categoria_id',
                'productos.id as producto_id',
                'productos.nombre as producto',
                'productos.sku',
                'productos.imagen',
                DB::raw('SUM(detalle_ventas.cantidad) as unidades'),
                DB::raw('SUM(detalle_ventas.subtotal) as total')
            )
            ->groupBy('productos.categoria_id', 'productos.id', 'productos.nombre', 'productos.sku', 'productos.imagen')
            ->orderByDesc('total')
            ->get()
            ->groupBy('categoria_id');

        $ventasDelDia = Venta::with('cliente')
            ->whereDate('fecha_venta', $fecha)
            ->where('estado', '!=', 'cancelado')
            ->orderByDesc('created_at')
            ->get();

        $ajustes = Movimiento::with('producto', 'user')
            ->whereDate('fecha', $fecha)
            ->where(function ($q) {
                $q->where('motivo', 'not like', 'Venta %')->orWhereNull('motivo');
            })
            ->get();

        return view('movimientos.show', compact(
            'fechaCarbon', 'fecha', 'porCategoria', 'totalDia',
            'productosPorCategoria', 'ventasDelDia', 'ajustes'
        ));
    }

    public function create()
    {
        $categorias = Categoria::with('subcategorias')->orderBy('nombre')->get();
        $productos  = Producto::where('activo', true)->orderBy('nombre')->get();
        return view('movimientos.create', compact('categorias', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'tipo'        => 'required|in:entrada,salida',
            'cantidad'    => 'required|integer|min:1',
            'motivo'      => 'nullable|string|max:255',
            'fecha'       => 'required|date',
        ]);

        $producto = Producto::find($request->producto_id);

        if ($request->tipo === 'salida' && $producto->stock < $request->cantidad) {
            return back()->withErrors(['cantidad' => 'Stock insuficiente. Solo hay ' . $producto->stock . ' unidades.'])->withInput();
        }

        Movimiento::create([
            'producto_id' => $request->producto_id,
            'user_id'     => auth()->id(),
            'tipo'        => $request->tipo,
            'cantidad'    => $request->cantidad,
            'motivo'      => $request->motivo,
            'fecha'       => $request->fecha,
        ]);

        if ($request->tipo === 'entrada') {
            $producto->increment('stock', $request->cantidad);
        } else {
            $producto->decrement('stock', $request->cantidad);
        }

        $nuevoStock = $producto->fresh()->stock;
        if ($nuevoStock <= 0) {
            $estado = 'agotado';
        } elseif ($nuevoStock <= $producto->stock_minimo) {
            $estado = 'bajo_stock';
        } else {
            $estado = 'en_stock';
        }
        $producto->update(['estado' => $estado]);

        return redirect()->route('movimientos.index')->with('success', 'Movimiento registrado. Stock actualizado.');
    }

    public function destroy(Movimiento $movimiento)
    {
        $movimiento->delete();
        return redirect()->route('movimientos.index')->with('success', 'Movimiento eliminado.');
    }
}
