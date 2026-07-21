<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Módulos y acciones que forman el catálogo inicial de permisos.
     * Esto es solo el punto de partida: desde el CRUD de Roles se pueden
     * agregar más permisos en caliente, sin tocar este archivo.
     *
     * @var array<int, string>
     */
    private array $modulos = [
        'productos', 'categorias', 'subcategorias', 'clientes',
        'movimientos', 'ventas', 'reportes', 'usuarios', 'roles',
    ];

    /** @var array<int, string> */
    private array $acciones = ['ver', 'crear', 'editar', 'eliminar'];

    public function run(): void
    {
        foreach ($this->modulos as $modulo) {
            foreach ($this->acciones as $accion) {
                Permission::firstOrCreate(['name' => "{$modulo}.{$accion}", 'guard_name' => 'web']);
            }
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        $empleado = Role::firstOrCreate(['name' => 'Empleado', 'guard_name' => 'web']);
        $empleado->syncPermissions(
            Permission::where('name', 'like', '%.ver')->get()
        );

        $primerUsuario = User::orderBy('id')->first();

        if ($primerUsuario && ! $primerUsuario->hasRole('Super Admin')) {
            $primerUsuario->assignRole($superAdmin);
        }
    }
}
