<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SubcategoriasExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function __construct(protected Builder $query) {}

    public function query(): Builder
    {
        return $this->query;
    }

    public function headings(): array
    {
        return ['Nombre', 'Categoría', 'Descripción', 'Productos', 'Activo'];
    }

    public function map($subcategoria): array
    {
        return [
            $subcategoria->nombre,
            $subcategoria->categoria?->nombre,
            $subcategoria->descripcion,
            $subcategoria->productos_count,
            $subcategoria->activo ? 'Sí' : 'No',
        ];
    }
}
