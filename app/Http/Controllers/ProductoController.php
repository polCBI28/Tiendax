<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Subcategoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index(Request $request)
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
            $query->where(fn($sq) => $sq->where('nombre', 'like', "%$q%")->orWhere('sku', 'like', "%$q%"));
        }

        $ordenar  = $request->get('ordenar', 'nombre');
        $dir      = $request->get('dir', 'asc');
        $columnas = ['nombre', 'sku', 'precio_venta', 'precio_costo', 'stock', 'unidades_vendidas', 'ingresos_generados'];
        if (in_array($ordenar, $columnas)) {
            $query->orderBy($ordenar, $dir === 'desc' ? 'desc' : 'asc');
        }

        $productos      = $query->paginate(20)->withQueryString();
        $categorias     = Categoria::orderBy('nombre')->get();
        $totalProductos = Producto::count();
        $bajoStock      = Producto::where('estado', 'bajo_stock')->count();
        $agotados       = Producto::where('estado', 'agotado')->count();
        $valorTotal     = Producto::sum(DB::raw('precio_venta * stock'));
        $nuevosEsteMes  = Producto::whereMonth('created_at', now()->month)->count();

        return view('productos.index', compact(
            'productos', 'categorias', 'totalProductos',
            'bajoStock', 'agotados', 'valorTotal', 'nuevosEsteMes'
        ));
    }

    public function create()
    {
        $categorias    = Categoria::where('activo', true)->get();
        $subcategorias = Subcategoria::where('activo', true)->get();
        return view('productos.create', compact('categorias', 'subcategorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'          => 'required|string|max:255',
            'sku'             => 'required|string|unique:productos,sku',
            'categoria_id'    => 'required|exists:categorias,id',
            'subcategoria_id' => 'nullable|exists:subcategorias,id',
            'precio_venta'    => 'required|numeric|min:0',
            'precio_costo'    => 'nullable|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'stock_minimo'    => 'required|integer|min:0',
            'imagen'          => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'nombre', 'sku', 'categoria_id', 'subcategoria_id',
            'descripcion', 'precio_venta', 'precio_costo',
            'stock', 'stock_minimo'
        ]);

        $stock    = (int) $data['stock'];
        $minimo   = (int) $data['stock_minimo'];
        $data['estado'] = $stock <= 0 ? 'agotado' : ($stock <= $minimo ? 'bajo_stock' : 'en_stock');
        $data['activo'] = $request->has('activo') ? 1 : 0;

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        Producto::create($data);
        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente.');
    }

    public function show(Producto $producto)
    {
        $producto->load('categoria', 'subcategoria', 'movimientos');
        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        $categorias    = Categoria::where('activo', true)->get();
        $subcategorias = Subcategoria::where('activo', true)->get();
        return view('productos.edit', compact('producto', 'categorias', 'subcategorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre'          => 'required|string|max:255',
            'sku'             => 'required|string|unique:productos,sku,' . $producto->id,
            'categoria_id'    => 'required|exists:categorias,id',
            'subcategoria_id' => 'nullable|exists:subcategorias,id',
            'precio_venta'    => 'required|numeric|min:0',
            'precio_costo'    => 'nullable|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'stock_minimo'    => 'required|integer|min:0',
            'imagen'          => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'nombre', 'sku', 'categoria_id', 'subcategoria_id',
            'descripcion', 'precio_venta', 'precio_costo',
            'stock', 'stock_minimo'
        ]);

        $stock    = (int) $data['stock'];
        $minimo   = (int) $data['stock_minimo'];
        $data['estado'] = $stock <= 0 ? 'agotado' : ($stock <= $minimo ? 'bajo_stock' : 'en_stock');
        $data['activo'] = $request->has('activo') ? 1 : 0;

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update($data);
        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');
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
            $query->where(fn($sq) => $sq->where('nombre', 'like', "%$q%")->orWhere('sku', 'like', "%$q%"));
        }

        $ordenar = $request->get('ordenar', 'nombre');
        $dir     = $request->get('dir', 'asc');
        $columnas = ['nombre', 'sku', 'precio_venta', 'precio_costo', 'stock', 'unidades_vendidas', 'ingresos_generados'];
        if (in_array($ordenar, $columnas)) {
            $query->orderBy($ordenar, $dir === 'desc' ? 'desc' : 'asc');
        }

        $productos      = $query->paginate(20)->withQueryString();
        $categorias     = Categoria::orderBy('nombre')->get();
        $totalProductos = Producto::count();
        $bajoStock      = Producto::where('estado', 'bajo_stock')->count();
        $agotados       = Producto::where('estado', 'agotado')->count();
        $valorTotal     = Producto::sum(DB::raw('precio_venta * stock'));

        return view('productos.detalle', compact(
            'productos', 'categorias',
            'totalProductos', 'bajoStock', 'agotados', 'valorTotal'
        ));
    }

    public function buscar(Request $request)
    {
        $query    = $request->get('q');
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