<?php

use App\Models\Configuracion;
use App\Models\User;
use App\Services\LogoProcessor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Storage::fake('public');
    Configuracion::olvidarCache();
    Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'Empleado', 'guard_name' => 'web']);

    $this->superAdmin = User::factory()->create();
    $this->superAdmin->assignRole('Super Admin');
});

test('la página de logo carga correctamente para un Super Admin', function () {
    $this->actingAs($this->superAdmin)
        ->get(route('settings.logo'))
        ->assertOk();
});

test('un usuario sin el rol Super Admin no puede acceder al logo', function () {
    $empleado = User::factory()->create();
    $empleado->assignRole('Empleado');

    $this->actingAs($empleado)
        ->get(route('settings.logo'))
        ->assertForbidden();
});

test('se puede subir un logo nuevo y queda disponible en la configuración', function () {
    $this->actingAs($this->superAdmin);

    Volt::test('settings.logo')
        ->set('logo', UploadedFile::fake()->image('logo.png', 300, 100))
        ->call('guardar')
        ->assertHasNoErrors();

    $configuracion = Configuracion::actual();

    expect($configuracion->logo_path)->not->toBeNull();
    Storage::disk('public')->assertExists($configuracion->logo_path);
});

test('se puede restaurar el logo predeterminado', function () {
    $this->actingAs($this->superAdmin);

    Storage::disk('public')->put('logo/logo-existente.png', 'contenido');
    $configuracion = Configuracion::actual();
    $configuracion->update(['logo_path' => 'logo/logo-existente.png']);
    Configuracion::olvidarCache();

    Volt::test('settings.logo')
        ->call('quitarLogo')
        ->assertHasNoErrors();

    expect(Configuracion::actual()->logo_path)->toBeNull();
    Storage::disk('public')->assertMissing('logo/logo-existente.png');
});

test('el procesador redimensiona una imagen que excede el máximo permitido', function () {
    $rutaTemporal = tempnam(sys_get_temp_dir(), 'logo').'.png';
    $imagen = imagecreatetruecolor(1200, 600);
    imagefill($imagen, 0, 0, imagecolorallocate($imagen, 255, 255, 255));
    imagepng($imagen, $rutaTemporal);
    imagedestroy($imagen);

    $contenido = (new LogoProcessor)->procesar($rutaTemporal, quitarFondo: false);
    $info = getimagesizefromstring($contenido);

    expect(max($info[0], $info[1]))->toBeLessThanOrEqual(800);

    unlink($rutaTemporal);
});

test('el procesador vuelve transparente el fondo sólido detectado en los bordes', function () {
    $rutaTemporal = tempnam(sys_get_temp_dir(), 'logo').'.png';
    $imagen = imagecreatetruecolor(100, 100);
    $blanco = imagecolorallocate($imagen, 255, 255, 255);
    $rojo = imagecolorallocate($imagen, 200, 30, 30);
    imagefill($imagen, 0, 0, $blanco);
    imagefilledrectangle($imagen, 40, 40, 60, 60, $rojo);
    imagepng($imagen, $rutaTemporal);
    imagedestroy($imagen);

    $contenido = (new LogoProcessor)->procesar($rutaTemporal, quitarFondo: true);
    $procesada = imagecreatefromstring($contenido);

    $colorEsquina = imagecolorsforindex($procesada, imagecolorat($procesada, 0, 0));
    $colorCentro = imagecolorsforindex($procesada, imagecolorat($procesada, 50, 50));

    expect($colorEsquina['alpha'])->toBe(127);
    expect($colorCentro['alpha'])->toBe(0);

    imagedestroy($procesada);
    unlink($rutaTemporal);
});
