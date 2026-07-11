<?php

namespace App\Http\Controllers;

use App\Models\Cliente;

class ClienteController extends Controller
{
    public function show(Cliente $cliente)
    {
        $cliente->load('ventas');

        return view('clientes.show', compact('cliente'));
    }
}
