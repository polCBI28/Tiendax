<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CategoriasExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function __construct(protected Builder $query) {}

    public function query(): Builder
    {
        return $this->query;
    }

    public function headings(): array
    {
        return ['Nombre', 'Descripción', 'Productos', 'Activo'];
    }

    public function map($categoria): array
    {
        return [
            $categoria->nombre,
            $categoria->descripcion,
            $categoria->productos_count,
            $categoria->activo ? 'Sí' : 'No',
        ];
    }
}
