<?php

namespace App\Http\Controllers;

use App\Models\Subcategoria;
use App\Models\Categoria;
use Illuminate\Http\Request;

class SubcategoriaController extends Controller
{
    public function index()
    {
        $subcategorias = Subcategoria::with('categoria')->get();
        return view('subcategorias.index', compact('subcategorias'));
    }

    public function create()
    {
        $categorias = Categoria::where('activo', true)->get();
        return view('subcategorias.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string',
        ]);

        Subcategoria::create($request->only(['categoria_id', 'nombre', 'descripcion', 'activo']));
        return redirect()->route('subcategorias.index')->with('success', 'Subcategoría creada correctamente.');
    }

    public function show(Subcategoria $subcategoria)
    {
        $subcategoria->load('categoria', 'productos');
        return view('subcategorias.show', compact('subcategoria'));
    }

    public function edit(Subcategoria $subcategoria)
    {
        $categorias = Categoria::where('activo', true)->get();
        return view('subcategorias.edit', compact('subcategoria', 'categorias'));
    }

    public function update(Request $request, Subcategoria $subcategoria)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string',
        ]);

        $subcategoria->update($request->only(['categoria_id', 'nombre', 'descripcion', 'activo']));
        return redirect()->route('subcategorias.index')->with('success', 'Subcategoría actualizada correctamente.');
    }

    public function destroy(Subcategoria $subcategoria)
    {
        $subcategoria->delete();
        return redirect()->route('subcategorias.index')->with('success', 'Subcategoría eliminada correctamente.');
    }
}