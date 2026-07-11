<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\SubcategoriaController;
use App\Http\Controllers\VentaController;
use App\Livewire\Admin\Buscar\BuscarIndex;
use App\Livewire\Admin\Categoria\CategoriaIndex;
use App\Livewire\Admin\Cliente\ClienteIndex;
use App\Livewire\Admin\DetalleVenta\DetalleVentaIndex;
use App\Livewire\Admin\Movimiento\MovimientoIndex;
use App\Livewire\Admin\Producto\ProductoIndex;
use App\Livewire\Admin\Reporte\ReporteIndex;
use App\Livewire\Admin\Subcategoria\SubcategoriaIndex;
use App\Livewire\Admin\Venta\VentaIndex;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Redirigir raíz al dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Rutas protegidas
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        $ventasHoy = Venta::whereDate('fecha_venta', today())->sum('total');
        $ventasAyer = Venta::whereDate('fecha_venta', today()->subDay())->sum('total');
        $bajoStock = Producto::where('estado', 'bajo_stock')->count();
        $ventasPendientes = Venta::where('estado', 'pendiente')->count();
        $ultimasVentas = Venta::with('detalles.producto', 'cliente')->latest()->limit(5)->get();

        $crecimiento = $ventasAyer > 0
            ? round((($ventasHoy - $ventasAyer) / $ventasAyer) * 100, 1)
            : ($ventasHoy > 0 ? 100 : 0);

        // Ventas reales de los últimos 7 días para el gráfico
        $diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
        $labelsSemanales = [];
        $datosSemanales = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i);
            $labelsSemanales[] = $diasSemana[$fecha->dayOfWeek].' '.$fecha->format('d/m');
            $datosSemanales[] = (float) Venta::whereDate('fecha_venta', $fecha->format('Y-m-d'))->sum('total');
        }

        return view('dashboard', compact(
            'ventasHoy', 'bajoStock', 'ventasPendientes',
            'ultimasVentas', 'crecimiento',
            'labelsSemanales', 'datosSemanales'
        ));
    })->name('dashboard');

    // Búsqueda global
    Route::get('/buscar', BuscarIndex::class)->name('buscar');

    // Catálogo
    Route::get('/categorias', CategoriaIndex::class)->name('categorias.index');
    Route::get('/categorias/{categoria}', [CategoriaController::class, 'show'])->name('categorias.show');
    Route::get('/subcategorias', SubcategoriaIndex::class)->name('subcategorias.index');
    Route::get('/subcategorias/{subcategoria}', [SubcategoriaController::class, 'show'])->name('subcategorias.show');

    // Inventario
    Route::get('/productos/buscar', [ProductoController::class, 'buscar'])->name('productos.buscar');
    Route::get('/productos/detalle', [ProductoController::class, 'detalle'])->name('productos.detalle');
    Route::get('/productos', ProductoIndex::class)->name('productos.index');
    Route::get('/productos/{producto}', [ProductoController::class, 'show'])->name('productos.show');
    Route::get('/movimientos', MovimientoIndex::class)->name('movimientos.index');
    Route::get('/movimientos/{fecha}', [MovimientoController::class, 'show'])->name('movimientos.show');

    // Ventas
    Route::get('/ventas/detalle', [VentaController::class, 'detalle'])->name('ventas.detalle');
    Route::patch('/ventas/{venta}/completar-pago', [VentaController::class, 'completarPago'])->name('ventas.completar-pago');
    Route::get('/clientes', ClienteIndex::class)->name('clientes.index');
    Route::get('/clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
    Route::get('/ventas', VentaIndex::class)->name('ventas.index');
    Route::get('/ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show');
    Route::get('/detalle-ventas', DetalleVentaIndex::class)->name('detalle-ventas.index');

    // Reportes
    Route::get('/reportes', ReporteIndex::class)->name('reportes.index');
    Route::get('/reportes/exportar-csv', [ReporteController::class, 'exportarCsv'])->name('reportes.exportar');

    // Settings
    Volt::route('/settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('/settings/password', 'settings.password')->name('settings.password');
    Volt::route('/settings/appearance', 'settings.appearance')->name('settings.appearance');

});

// Autenticación
require __DIR__.'/auth.php';
