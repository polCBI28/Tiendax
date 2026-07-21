<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\Movimiento;
use App\Models\Producto;
use App\Models\Subcategoria;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (Categoria::query()->exists()) {
            $this->command?->warn('DemoSeeder: ya hay categorías en la base de datos, se omite para no duplicar.');

            return;
        }

        $user = User::first() ?? User::factory()->create();

        $categorias = $this->seedCategorias();
        $productos = $this->seedProductos($categorias);
        $clientes = $this->seedClientes();
        $this->seedVentas($user, $clientes, $productos);
        $this->seedMovimientosSueltos($user, $productos);
    }

    /**
     * @return array<int, Categoria>
     */
    private function seedCategorias(): array
    {
        $definiciones = [
            'Ropa y Moda' => ['icono' => 'checkroom', 'subs' => ['Camisas', 'Pantalones', 'Chaquetas']],
            'Tecnología' => ['icono' => 'devices', 'subs' => ['Audio', 'Cómputo', 'Cargadores']],
            'Hogar' => ['icono' => 'home', 'subs' => ['Cocina', 'Decoración', 'Limpieza']],
            'Salud y Belleza' => ['icono' => 'spa', 'subs' => ['Cuidado Facial', 'Perfumes', 'Cuidado Capilar']],
            'Deportes' => ['icono' => 'sports_soccer', 'subs' => ['Fitness', 'Ciclismo', 'Natación']],
            'Juguetes' => ['icono' => 'toys', 'subs' => ['Educativos', 'Figuras', 'Juegos de Mesa']],
            'Accesorios' => ['icono' => 'diamond', 'subs' => ['Bolsos', 'Joyería', 'Relojes']],
            'Alimentos' => ['icono' => 'restaurant', 'subs' => ['Snacks', 'Bebidas', 'Dulces']],
        ];

        $categorias = [];

        foreach ($definiciones as $nombre => $data) {
            $categoria = Categoria::create([
                'nombre' => $nombre,
                'icono' => $data['icono'],
                'descripcion' => "Productos de la categoría {$nombre}.",
                'activo' => true,
            ]);

            $categoria->subs = collect($data['subs'])->map(
                fn ($subNombre) => Subcategoria::create([
                    'categoria_id' => $categoria->id,
                    'nombre' => $subNombre,
                    'descripcion' => "Subcategoría {$subNombre} de {$nombre}.",
                    'activo' => true,
                ])
            );

            $categorias[] = $categoria;
        }

        return $categorias;
    }

    /**
     * @param  array<int, Categoria>  $categorias
     * @return array<int, Producto>
     */
    private function seedProductos(array $categorias): array
    {
        $nombresPorSub = [
            'Camisas' => ['Camisa Manga Larga', 'Camisa Casual Slim', 'Camisa a Cuadros', 'Camisa Lino Premium'],
            'Pantalones' => ['Pantalón Jean Clásico', 'Pantalón Jogger', 'Pantalón Cargo', 'Pantalón de Vestir'],
            'Chaquetas' => ['Chaqueta de Cuero', 'Chaqueta Impermeable', 'Chaqueta Denim', 'Chaqueta Deportiva'],
            'Audio' => ['Audífonos Bluetooth', 'Parlante Portátil', 'Micrófono USB', 'Audífonos Gamer'],
            'Cómputo' => ['Mouse Inalámbrico', 'Teclado Mecánico', 'Hub USB-C', 'Webcam Full HD'],
            'Cargadores' => ['Cargador Rápido 20W', 'Power Bank 10000mAh', 'Cable USB-C 2m', 'Cargador Inalámbrico'],
            'Cocina' => ['Set de Ollas Antiadherentes', 'Licuadora 3 Velocidades', 'Juego de Cuchillos', 'Tabla de Picar Bambú'],
            'Decoración' => ['Cuadro Decorativo', 'Set de Velas Aromáticas', 'Espejo de Pared', 'Jarrón Cerámico'],
            'Limpieza' => ['Kit de Limpieza Multiusos', 'Aspiradora de Mano', 'Trapeador Giratorio', 'Organizador Plástico'],
            'Cuidado Facial' => ['Crema Hidratante Facial', 'Serum Vitamina C', 'Protector Solar FPS50', 'Mascarilla de Arcilla'],
            'Perfumes' => ['Perfume Floral 100ml', 'Perfume Amaderado 100ml', 'Colonia Cítrica 50ml', 'Perfume Oriental 75ml'],
            'Cuidado Capilar' => ['Shampoo Reparador', 'Acondicionador Nutritivo', 'Aceite Capilar Argán', 'Mascarilla Capilar'],
            'Fitness' => ['Set de Mancuernas', 'Colchoneta de Yoga', 'Banda de Resistencia', 'Cuerda para Saltar'],
            'Ciclismo' => ['Casco de Ciclismo', 'Guantes de Ciclismo', 'Luces LED para Bici', 'Botella Térmica Deportiva'],
            'Natación' => ['Gafas de Natación', 'Gorro de Silicona', 'Tabla de Flotación', 'Short de Baño'],
            'Educativos' => ['Rompecabezas 500 Piezas', 'Bloques de Construcción', 'Juego de Memoria', 'Kit de Ciencia'],
            'Figuras' => ['Figura de Acción Coleccionable', 'Figura Articulada', 'Set de Figuras Mini', 'Figura de Colección Deluxe'],
            'Juegos de Mesa' => ['Juego de Cartas Familiar', 'Juego de Estrategia', 'Dominó Clásico', 'Juego de Preguntas'],
            'Bolsos' => ['Mochila Urbana', 'Bolso de Mano', 'Cartera de Cuero', 'Riñonera Deportiva'],
            'Joyería' => ['Collar Acero Inoxidable', 'Pulsera de Plata', 'Aretes Chapados en Oro', 'Anillo Ajustable'],
            'Relojes' => ['Reloj Deportivo Digital', 'Reloj Clásico Cuero', 'Smartwatch Básico', 'Reloj Minimalista'],
            'Snacks' => ['Mix de Frutos Secos', 'Papas Artesanales', 'Barra de Cereal', 'Galletas Integrales'],
            'Bebidas' => ['Jugo Natural 1L', 'Agua Saborizada', 'Café Molido Premium', 'Té en Hebras'],
            'Dulces' => ['Chocolate Artesanal', 'Caramelos Surtidos', 'Gomitas de Frutas', 'Turrón Tradicional'],
        ];

        // Rango de precio de costo (min, max) según categoría, para que se vea realista.
        $rangoCostoPorCategoria = [
            'Ropa y Moda' => [25, 120],
            'Tecnología' => [30, 250],
            'Hogar' => [15, 150],
            'Salud y Belleza' => [8, 60],
            'Deportes' => [15, 130],
            'Juguetes' => [10, 80],
            'Accesorios' => [12, 200],
            'Alimentos' => [2, 18],
        ];

        $productos = [];
        $skuSeq = 1;

        foreach ($categorias as $categoria) {
            [$costoMin, $costoMax] = $rangoCostoPorCategoria[$categoria->nombre] ?? [10, 100];

            foreach ($categoria->subs as $sub) {
                $nombres = $nombresPorSub[$sub->nombre] ?? ["{$sub->nombre} Producto"];

                foreach ($nombres as $nombre) {
                    $costo = fake()->randomFloat(2, $costoMin, $costoMax);
                    $venta = round($costo * fake()->randomFloat(2, 1.3, 2.2), 2);
                    $stockMinimo = fake()->numberBetween(3, 8);

                    // Distribución realista: mayoría en stock, algunos bajos, pocos agotados.
                    $stock = match (true) {
                        fake()->boolean(10) => 0,
                        fake()->boolean(20) => fake()->numberBetween(1, $stockMinimo),
                        default => fake()->numberBetween($stockMinimo + 1, 120),
                    };

                    $estado = $stock <= 0 ? 'agotado' : ($stock <= $stockMinimo ? 'bajo_stock' : 'en_stock');

                    $productos[] = Producto::create([
                        'categoria_id' => $categoria->id,
                        'subcategoria_id' => $sub->id,
                        'nombre' => $nombre,
                        'sku' => 'SKU-'.str_pad((string) $skuSeq++, 5, '0', STR_PAD_LEFT),
                        'descripcion' => "{$nombre} — calidad garantizada.",
                        'precio_venta' => $venta,
                        'precio_costo' => $costo,
                        'stock' => $stock,
                        'stock_minimo' => $stockMinimo,
                        'estado' => $estado,
                        'activo' => true,
                    ]);
                }
            }
        }

        return $productos;
    }

    /**
     * @return array<int, Cliente>
     */
    private function seedClientes(): array
    {
        $nombres = [
            'María González', 'Carlos Ramírez', 'Ana Torres', 'Luis Fernández', 'Sofía Vargas',
            'Diego Chávez', 'Camila Rojas', 'Jorge Medina', 'Valentina Cruz', 'Miguel Herrera',
            'Daniela Paredes', 'Andrés Salazar', 'Lucía Castillo', 'Ricardo Flores', 'Paula Guzmán',
        ];

        return collect($nombres)->map(function ($nombre) {
            $slug = Str::ascii(Str::lower(str_replace(' ', '.', $nombre)));

            return Cliente::create([
                'nombre' => $nombre,
                'documento' => (string) fake()->unique()->numberBetween(10000000, 79999999),
                'telefono' => '9'.fake()->numerify('########'),
                'email' => "{$slug}@example.com",
            ]);
        })->all();
    }

    /**
     * @param  array<int, Cliente>  $clientes
     * @param  array<int, Producto>  $productos
     */
    private function seedVentas(User $user, array $clientes, array $productos): void
    {
        $numeroBase = 1;

        for ($i = 0; $i < 60; $i++) {
            $fecha = now()->subDays(fake()->numberBetween(0, 44))->setTime(fake()->numberBetween(9, 20), fake()->numberBetween(0, 59));

            $disponibles = array_values(array_filter($productos, fn (Producto $p) => $p->fresh()->stock > 0));
            if (empty($disponibles)) {
                break;
            }

            $lineas = fake()->numberBetween(1, 4);
            $seleccionados = collect($disponibles)->shuffle()->take($lineas);

            $subtotal = 0;
            $itemsData = [];

            foreach ($seleccionados as $producto) {
                $productoFresco = $producto->fresh();
                if ($productoFresco->stock <= 0) {
                    continue;
                }

                $cantidad = fake()->numberBetween(1, min(3, $productoFresco->stock));
                $adicional = fake()->boolean(15) ? fake()->randomFloat(2, 2, 10) : 0;
                $precioUnitario = (float) $productoFresco->precio_venta + $adicional;

                $itemsData[] = [
                    'producto' => $productoFresco,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'adicional' => $adicional,
                    'subtotal' => $precioUnitario * $cantidad,
                ];

                $subtotal += $precioUnitario * $cantidad;
            }

            if (empty($itemsData)) {
                continue;
            }

            $descuentoTipo = null;
            $descuentoValor = 0;
            $descuento = 0;
            if (fake()->boolean(30)) {
                $descuentoTipo = fake()->randomElement(['monto', 'porcentaje']);
                $descuentoValor = $descuentoTipo === 'porcentaje' ? fake()->numberBetween(5, 20) : fake()->randomFloat(2, 5, 30);
                $descuento = $descuentoTipo === 'porcentaje'
                    ? round($subtotal * $descuentoValor / 100, 2)
                    : min($descuentoValor, $subtotal);
            }

            $recargoTipo = null;
            $recargoValor = 0;
            $recargo = 0;
            if (fake()->boolean(10)) {
                $recargoTipo = fake()->randomElement(['monto', 'porcentaje']);
                $base = $subtotal - $descuento;
                $recargoValor = $recargoTipo === 'porcentaje' ? fake()->numberBetween(5, 15) : fake()->randomFloat(2, 5, 20);
                $recargo = $recargoTipo === 'porcentaje' ? round($base * $recargoValor / 100, 2) : $recargoValor;
            }

            $total = $subtotal - $descuento + $recargo;

            $estado = fake()->randomElement([
                'completado', 'completado', 'completado', 'completado', 'completado', 'completado', 'completado',
                'pendiente', 'pendiente',
                'cancelado',
                'borrador',
            ]);

            $adelanto = match ($estado) {
                'completado' => $total,
                'pendiente' => round($total * fake()->randomFloat(2, 0.2, 0.7), 2),
                default => 0,
            };

            $venta = Venta::create([
                'cliente_id' => fake()->boolean(70) ? fake()->randomElement($clientes)->id : null,
                'user_id' => $user->id,
                'numero_boleta' => 'B001-'.str_pad((string) $numeroBase++, 6, '0', STR_PAD_LEFT),
                'fecha_venta' => $fecha,
                'descripcion' => fake()->boolean(25) ? fake()->sentence(6) : null,
                'total' => $total,
                'adelanto' => $adelanto,
                'descuento_tipo' => $descuentoTipo,
                'descuento_valor' => $descuentoValor,
                'recargo_tipo' => $recargoTipo,
                'recargo_valor' => $recargoValor,
                'estado' => $estado,
                'created_at' => $fecha,
                'updated_at' => $fecha,
            ]);

            if ($estado === 'cancelado') {
                continue;
            }

            foreach ($itemsData as $item) {
                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $item['producto']->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'adicional' => $item['adicional'],
                    'subtotal' => $item['subtotal'],
                ]);

                $producto = $item['producto'];
                $producto->decrement('stock', $item['cantidad']);
                $nuevoStock = $producto->fresh()->stock;
                $producto->update([
                    'estado' => $nuevoStock <= 0 ? 'agotado'
                        : ($nuevoStock <= $producto->stock_minimo ? 'bajo_stock' : 'en_stock'),
                ]);

                Movimiento::create([
                    'producto_id' => $producto->id,
                    'user_id' => $user->id,
                    'tipo' => 'salida',
                    'cantidad' => $item['cantidad'],
                    'motivo' => 'Venta '.$venta->numero_boleta,
                    'fecha' => $fecha->format('Y-m-d'),
                    'created_at' => $fecha,
                    'updated_at' => $fecha,
                ]);
            }
        }
    }

    /**
     * @param  array<int, Producto>  $productos
     */
    private function seedMovimientosSueltos(User $user, array $productos): void
    {
        $motivosAjuste = [
            'Producto dañado en almacén', 'Merma por vencimiento', 'Corrección de inventario físico',
            'Devolución de cliente', 'Producto extraviado',
        ];

        // Entradas: reabastecimiento de stock.
        for ($i = 0; $i < 20; $i++) {
            $producto = fake()->randomElement($productos)->fresh();
            $cantidad = fake()->numberBetween(10, 50);
            $fecha = now()->subDays(fake()->numberBetween(0, 44));

            Movimiento::create([
                'producto_id' => $producto->id,
                'user_id' => $user->id,
                'tipo' => 'entrada',
                'cantidad' => $cantidad,
                'motivo' => 'Reabastecimiento de proveedor',
                'fecha' => $fecha->format('Y-m-d'),
                'created_at' => $fecha,
                'updated_at' => $fecha,
            ]);

            $producto->increment('stock', $cantidad);
            $nuevoStock = $producto->fresh()->stock;
            $producto->update([
                'estado' => $nuevoStock <= 0 ? 'agotado'
                    : ($nuevoStock <= $producto->stock_minimo ? 'bajo_stock' : 'en_stock'),
            ]);
        }

        // Ajustes manuales (salidas sin venta asociada).
        for ($i = 0; $i < 8; $i++) {
            $producto = fake()->randomElement($productos)->fresh();
            if ($producto->stock <= 0) {
                continue;
            }

            $cantidad = fake()->numberBetween(1, min(5, $producto->stock));
            $fecha = now()->subDays(fake()->numberBetween(0, 44));

            Movimiento::create([
                'producto_id' => $producto->id,
                'user_id' => $user->id,
                'tipo' => 'salida',
                'cantidad' => $cantidad,
                'motivo' => fake()->randomElement($motivosAjuste),
                'fecha' => $fecha->format('Y-m-d'),
                'created_at' => $fecha,
                'updated_at' => $fecha,
            ]);

            $producto->decrement('stock', $cantidad);
            $nuevoStock = $producto->fresh()->stock;
            $producto->update([
                'estado' => $nuevoStock <= 0 ? 'agotado'
                    : ($nuevoStock <= $producto->stock_minimo ? 'bajo_stock' : 'en_stock'),
            ]);
        }
    }
}
