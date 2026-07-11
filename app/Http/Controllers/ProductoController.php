<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function show(Producto $producto)
    {
        $producto->load('categoria', 'subcategoria', 'movimientos');

        return view('productos.show', compact('producto'));
    }

    public function detalle(Request $request)
    {
        $query = Producto::with('categoria', 'subcategoria')
            ->withCount('detalleVentas as num_ventas')
            ->withSum('detalleVentas as unidades_vendidas', 'cantidad')
            ->withSum('detalleVentas as ingresos_generados', 'subtotal');

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn ($sq) => $sq->where('nombre', 'like', "%$q%")->orWhere('sku', 'like', "%$q%"));
        }

        $ordenar = $request->get('ordenar', 'nombre');
        $dir = $request->get('dir', 'asc');
        $columnas = ['nombre', 'sku', 'precio_venta', 'precio_costo', 'stock', 'unidades_vendidas', 'ingresos_generados'];
        if (in_array($ordenar, $columnas)) {
            $query->orderBy($ordenar, $dir === 'desc' ? 'desc' : 'asc');
        }

        $productos = $query->paginate(20)->withQueryString();
        $categorias = Categoria::orderBy('nombre')->get();
        $totalProductos = Producto::count();
        $bajoStock = Producto::where('estado', 'bajo_stock')->count();
        $agotados = Producto::where('estado', 'agotado')->count();
        $valorTotal = Producto::sum(DB::raw('precio_venta * stock'));

        return view('productos.detalle', compact(
            'productos', 'categorias',
            'totalProductos', 'bajoStock', 'agotados', 'valorTotal'
        ));
    }

    public function buscar(Request $request)
    {
        $query = $request->get('q');
        $productos = Producto::where('activo', true)
            ->where(function ($q) use ($query) {
                $q->where('nombre', 'like', "%$query%")
                    ->orWhere('sku', 'like', "%$query%");
            })
            ->with('categoria')
            ->limit(10)
            ->get();

        return response()->json($productos);
    }
}
