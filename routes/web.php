<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\SubcategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\DetalleVentaController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\BuscarController;

// Redirigir raíz al dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Rutas protegidas
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        $ventasHoy        = \App\Models\Venta::whereDate('fecha_venta', today())->sum('total');
        $ventasAyer       = \App\Models\Venta::whereDate('fecha_venta', today()->subDay())->sum('total');
        $bajoStock        = \App\Models\Producto::where('estado', 'bajo_stock')->count();
        $ventasPendientes = \App\Models\Venta::where('estado', 'pendiente')->count();
        $ultimasVentas    = \App\Models\Venta::with('detalles.producto', 'cliente')->latest()->limit(5)->get();

        $crecimiento = $ventasAyer > 0
            ? round((($ventasHoy - $ventasAyer) / $ventasAyer) * 100, 1)
            : ($ventasHoy > 0 ? 100 : 0);

        // Ventas reales de los últimos 7 días para el gráfico
        $diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
        $labelsSemanales = [];
        $datosSemanales  = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i);
            $labelsSemanales[] = $diasSemana[$fecha->dayOfWeek] . ' ' . $fecha->format('d/m');
            $datosSemanales[]  = (float) \App\Models\Venta::whereDate('fecha_venta', $fecha->format('Y-m-d'))->sum('total');
        }

        return view('dashboard', compact(
            'ventasHoy', 'bajoStock', 'ventasPendientes',
            'ultimasVentas', 'crecimiento',
            'labelsSemanales', 'datosSemanales'
        ));
    })->name('dashboard');

    // Búsqueda global
    Route::get('/buscar', [BuscarController::class, 'index'])->name('buscar');

    // Catálogo
    Route::resource('categorias', CategoriaController::class);
    Route::resource('subcategorias', SubcategoriaController::class);

    // Inventario
    Route::get('/productos/buscar', [ProductoController::class, 'buscar'])->name('productos.buscar');
    Route::get('/productos/detalle', [ProductoController::class, 'detalle'])->name('productos.detalle');
    Route::resource('productos', ProductoController::class);
    Route::get('/movimientos', [MovimientoController::class, 'index'])->name('movimientos.index');
    Route::get('/movimientos/crear', [MovimientoController::class, 'create'])->name('movimientos.create');
    Route::post('/movimientos', [MovimientoController::class, 'store'])->name('movimientos.store');
    Route::get('/movimientos/{fecha}', [MovimientoController::class, 'show'])->name('movimientos.show');
    Route::delete('/movimientos/{movimiento}', [MovimientoController::class, 'destroy'])->name('movimientos.destroy');

    // Ventas
    Route::get('/ventas/detalle', [VentaController::class, 'detalle'])->name('ventas.detalle');
    Route::resource('clientes', ClienteController::class);
    Route::resource('ventas', VentaController::class);
    Route::resource('detalle-ventas', DetalleVentaController::class);

    // Reportes
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/exportar-csv', [ReporteController::class, 'exportarCsv'])->name('reportes.exportar');

});

// Autenticación
require __DIR__.'/auth.php';
