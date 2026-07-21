<?php

namespace App\Imports;

use App\Models\Cliente;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ClientesImport implements SkipsOnFailure, ToModel, WithHeadingRow, WithValidation
{
    use Importable, SkipsFailures;

    public int $creados = 0;

    public int $actualizados = 0;

    public function model(array $row)
    {
        $datos = [
            'nombre' => trim((string) $row['nombre']),
            'documento' => $row['documento'] ?? null,
            'telefono' => $row['telefono'] ?? null,
            'email' => $row['email'] ?? null,
        ];

        $existente = ! empty($datos['documento'])
            ? Cliente::where('documento', $datos['documento'])->first()
            : null;

        if ($existente) {
            $existente->update($datos);
            $this->actualizados++;

            return null;
        }

        $this->creados++;

        return new Cliente($datos);
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string'],
            'email' => ['nullable', 'email'],
        ];
    }
}
