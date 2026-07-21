<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClientesExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function __construct(protected Builder $query) {}

    public function query(): Builder
    {
        return $this->query;
    }

    public function headings(): array
    {
        return ['Nombre', 'Documento', 'Teléfono', 'Email'];
    }

    public function map($cliente): array
    {
        return [
            $cliente->nombre,
            $cliente->documento,
            $cliente->telefono,
            $cliente->email,
        ];
    }
}
