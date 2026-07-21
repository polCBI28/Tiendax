<?php

use App\Livewire\Admin\Buscar\BuscarIndex;
use App\Livewire\Admin\Categoria\CategoriaIndex;
use App\Livewire\Admin\Categoria\CategoriaShow;
use App\Livewire\Admin\Cliente\ClienteIndex;
use App\Livewire\Admin\Cliente\ClienteShow;
use App\Livewire\Admin\DetalleVenta\DetalleVentaIndex;
use App\Livewire\Admin\Movimiento\MovimientoIndex;
use App\Livewire\Admin\Movimiento\MovimientoShow;
use App\Livewire\Admin\Producto\ProductoIndex;
use App\Livewire\Admin\Producto\ProductoShow;
use App\Livewire\Admin\Reporte\ReporteIndex;
use App\Livewire\Admin\Rol\RolIndex;
use App\Livewire\Admin\Subcategoria\SubcategoriaIndex;
use App\Livewire\Admin\Subcategoria\SubcategoriaShow;
use App\Livewire\Admin\Usuario\UsuarioIndex;
use App\Livewire\Admin\Venta\VentaDetalleIndex;
use App\Livewire\Admin\Venta\VentaIndex;
use App\Livewire\Admin\Venta\VentaShow;
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
    Route::get('/categorias/{categoria}', CategoriaShow::class)->name('categorias.show');
    Route::get('/subcategorias', SubcategoriaIndex::class)->name('subcategorias.index');
    Route::get('/subcategorias/{subcategoria}', SubcategoriaShow::class)->name('subcategorias.show');

    // Inventario
    Route::get('/productos', ProductoIndex::class)->name('productos.index');
    Route::get('/productos/{producto}', ProductoShow::class)->name('productos.show');
    Route::get('/movimientos', MovimientoIndex::class)->name('movimientos.index');
    Route::get('/movimientos/{fecha}', MovimientoShow::class)->name('movimientos.show');

    // Ventas
    Route::get('/ventas/detalle', VentaDetalleIndex::class)->name('ventas.detalle');
    Route::get('/clientes', ClienteIndex::class)->name('clientes.index');
    Route::get('/clientes/{cliente}', ClienteShow::class)->name('clientes.show');
    Route::get('/ventas', VentaIndex::class)->name('ventas.index');
    Route::get('/ventas/{venta}', VentaShow::class)->name('ventas.show');
    Route::get('/detalle-ventas', DetalleVentaIndex::class)->name('detalle-ventas.index');

    // Reportes
    Route::get('/reportes', ReporteIndex::class)->name('reportes.index');

    // Administración (solo Super Admin)
    Route::middleware(['role:Super Admin'])->group(function () {
        Route::get('/usuarios', UsuarioIndex::class)->name('usuarios.index');
        Route::get('/roles', RolIndex::class)->name('roles.index');
    });

    // Settings
    Volt::route('/settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('/settings/password', 'settings.password')->name('settings.password');
    Volt::route('/settings/appearance', 'settings.appearance')->name('settings.appearance');

});

// Autenticación
require __DIR__.'/auth.php';
