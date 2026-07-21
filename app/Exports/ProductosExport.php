<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductosExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function __construct(protected Builder $query) {}

    public function query(): Builder
    {
        return $this->query;
    }

    public function headings(): array
    {
        return ['SKU', 'Nombre', 'Descripción', 'Categoría', 'Subcategoría', 'Precio Venta', 'Precio Costo', 'Stock', 'Stock Mínimo', 'Estado', 'Activo'];
    }

    public function map($producto): array
    {
        return [
            $producto->sku,
            $producto->nombre,
            $producto->descripcion,
            $producto->categoria?->nombre,
            $producto->subcategoria?->nombre,
            $producto->precio_venta,
            $producto->precio_costo,
            $producto->stock,
            $producto->stock_minimo,
            $producto->estado,
            $producto->activo ? 'Sí' : 'No',
        ];
    }
}
