<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias  = Categoria::withCount('productos')->get();
        $stockCritico = \App\Models\Producto::where('stock', '<=', \DB::raw('stock_minimo'))->count();
        return view('categorias.index', compact('categorias', 'stockCritico'));
    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'icono'       => 'nullable|string|max:100',
            'descripcion' => 'nullable|string',
            'imagen'      => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['nombre', 'icono', 'descripcion', 'activo']);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('categorias', 'public');
        }

        Categoria::create($data);
        return redirect()->route('categorias.index')->with('success', 'Categoría creada correctamente.');
    }

    public function show(Categoria $categoria)
    {
        $categoria->load([
            'subcategorias' => fn($q) => $q
                ->withCount('productos')
                ->withCount(['productos as en_stock' => fn($q) => $q->where('estado', 'en_stock')])
                ->withCount(['productos as bajo_stock' => fn($q) => $q->whereIn('estado', ['bajo_stock', 'agotado'])]),
        ]);

        $totalProductos = $categoria->subcategorias->sum('productos_count');
        $enStock        = $categoria->subcategorias->sum('en_stock');
        $conProblemas   = $categoria->subcategorias->sum('bajo_stock');

        return view('categorias.show', compact('categoria', 'totalProductos', 'enStock', 'conProblemas'));
    }

    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'icono'       => 'nullable|string|max:100',
            'descripcion' => 'nullable|string',
            'imagen'      => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['nombre', 'icono', 'descripcion', 'activo']);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('categorias', 'public');
        }

        $categoria->update($data);
        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();
        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada correctamente.');
    }
}