<?php

namespace App\Imports;

use App\Models\Categoria;
use App\Models\Subcategoria;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SubcategoriasImport implements SkipsOnFailure, ToModel, WithHeadingRow, WithValidation
{
    use Importable, SkipsFailures;

    public int $creados = 0;

    public int $actualizados = 0;

    public function model(array $row)
    {
        $categoria = Categoria::where('nombre', trim((string) $row['categoria']))->first();

        $datos = [
            'categoria_id' => $categoria?->id,
            'nombre' => trim((string) $row['nombre']),
            'descripcion' => $row['descripcion'] ?? null,
            'activo' => $this->parseBool($row['activo'] ?? 'si'),
        ];

        $existente = Subcategoria::where('nombre', $datos['nombre'])
            ->where('categoria_id', $categoria?->id)
            ->first();

        if ($existente) {
            $existente->update($datos);
            $this->actualizados++;

            return null;
        }

        $this->creados++;

        return new Subcategoria($datos);
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string'],
            'categoria' => ['required', 'exists:categorias,nombre'],
        ];
    }

    private function parseBool(mixed $value): bool
    {
        return ! in_array(Str::lower(trim((string) $value)), ['no', '0', 'false', 'inactivo'], true);
    }
}
