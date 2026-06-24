<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Venta;
use Illuminate\Http\Request;

class BuscarController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 2) {
            return view('buscar.index', ['q' => $q, 'productos' => collect(), 'clientes' => collect(), 'ventas' => collect()]);
        }

        $productos = Producto::where('nombre', 'like', "%$q%")
            ->orWhere('sku', 'like', "%$q%")
            ->with('categoria')
            ->limit(8)
            ->get();

        $clientes = Cliente::where('nombre', 'like', "%$q%")
            ->orWhere('documento', 'like', "%$q%")
            ->orWhere('email', 'like', "%$q%")
            ->limit(8)
            ->get();

        $ventas = Venta::where('numero_boleta', 'like', "%$q%")
            ->with('cliente')
            ->limit(8)
            ->get();

        return view('buscar.index', compact('q', 'productos', 'clientes', 'ventas'));
    }
}
