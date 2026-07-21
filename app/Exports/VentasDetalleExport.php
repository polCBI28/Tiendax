<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VentasDetalleExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function __construct(protected Builder $query) {}

    public function query(): Builder
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'Boleta', 'Fecha', 'Cliente', 'Unidades', 'Subtotal',
            'Descuento', 'Recargo', 'Total', 'Adelanto', 'Deuda', 'Estado', 'Vendedor',
        ];
    }

    public function map($venta): array
    {
        $subtotalVenta = $venta->detalles->sum('subtotal');
        $montoDescuento = $venta->descuento_valor > 0
            ? ($venta->descuento_tipo === 'porcentaje' ? round($subtotalVenta * $venta->descuento_valor / 100, 2) : $venta->descuento_valor)
            : 0;
        $montoRecargo = $venta->recargo_valor > 0
            ? ($venta->recargo_tipo === 'porcentaje' ? round(($subtotalVenta - $montoDescuento) * $venta->recargo_valor / 100, 2) : $venta->recargo_valor)
            : 0;
        $deuda = $venta->estado === 'completado' ? 0 : max(0, $venta->total - $venta->adelanto);

        return [
            $venta->numero_boleta,
            Carbon::parse($venta->fecha_venta)->format('d/m/Y'),
            $venta->cliente?->nombre ?? 'Sin cliente',
            $venta->detalles->sum('cantidad'),
            $subtotalVenta,
            $montoDescuento,
            $montoRecargo,
            $venta->total,
            $venta->adelanto,
            $deuda,
            $venta->estado,
            $venta->user?->name ?? '—',
        ];
    }
}
