<?php

namespace App\Http\Controllers;

use App\Models\Subcategoria;

class SubcategoriaController extends Controller
{
    public function show(Subcategoria $subcategoria)
    {
        $subcategoria->load('categoria', 'productos');

        return view('subcategorias.show', compact('subcategoria'));
    }
}
