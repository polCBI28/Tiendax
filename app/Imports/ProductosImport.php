<?php

namespace App\Imports;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Subcategoria;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductosImport implements SkipsOnFailure, ToModel, WithHeadingRow, WithValidation
{
    use Importable, SkipsFailures;

    public int $creados = 0;

    public int $actualizados = 0;

    public function model(array $row)
    {
        $categoria = Categoria::where('nombre', trim((string) $row['categoria']))->first();

        $subcategoria = ! empty($row['subcategoria'])
            ? Subcategoria::where('nombre', trim((string) $row['subcategoria']))
                ->where('categoria_id', $categoria?->id)
                ->first()
            : null;

        $stock = (int) ($row['stock'] ?? 0);
        $stockMinimo = (int) ($row['stock_minimo'] ?? 5);

        $datos = [
            'categoria_id' => $categoria?->id,
            'subcategoria_id' => $subcategoria?->id,
            'nombre' => trim((string) $row['nombre']),
            'descripcion' => ! empty($row['descripcion']) ? trim((string) $row['descripcion']) : null,
            'sku' => trim((string) $row['sku']),
            'precio_venta' => $row['precio_venta'],
            'precio_costo' => $row['precio_costo'] ?? null,
            'stock' => $stock,
            'stock_minimo' => $stockMinimo,
            'activo' => $this->parseBool($row['activo'] ?? 'si'),
        ];

        $datos['estado'] = $stock <= 0
            ? 'agotado'
            : ($stock <= $stockMinimo ? 'bajo_stock' : 'en_stock');

        $existente = Producto::where('sku', $datos['sku'])->first();

        if ($existente) {
            $existente->update($datos);
            $this->actualizados++;

            return null;
        }

        $this->creados++;

        return new Producto($datos);
    }

    public function rules(): array
    {
        return [
            'sku' => ['required', 'string'],
            'nombre' => ['required', 'string'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'categoria' => ['required', 'exists:categorias,nombre'],
            'precio_venta' => ['required', 'numeric', 'min:0'],
            'precio_costo' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'stock_minimo' => ['nullable', 'integer', 'min:0'],
        ];
    }

    private function parseBool(mixed $value): bool
    {
        return ! in_array(Str::lower(trim((string) $value)), ['no', '0', 'false', 'inactivo'], true);
    }
}
