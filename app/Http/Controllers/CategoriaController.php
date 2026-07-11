<?php

namespace App\Http\Controllers;

use App\Models\Categoria;

class CategoriaController extends Controller
{
    public function show(Categoria $categoria)
    {
        $categoria->load([
            'subcategorias' => fn ($q) => $q
                ->withCount('productos')
                ->withCount(['productos as en_stock' => fn ($q) => $q->where('estado', 'en_stock')])
                ->withCount(['productos as bajo_stock' => fn ($q) => $q->whereIn('estado', ['bajo_stock', 'agotado'])]),
        ]);

        $totalProductos = $categoria->subcategorias->sum('productos_count');
        $enStock = $categoria->subcategorias->sum('en_stock');
        $conProblemas = $categoria->subcategorias->sum('bajo_stock');

        return view('categorias.show', compact('categoria', 'totalProductos', 'enStock', 'conProblemas'));
    }
}
