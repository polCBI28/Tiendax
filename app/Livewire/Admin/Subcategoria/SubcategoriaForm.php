<?php

namespace App\Livewire\Admin\Subcategoria;

use App\Models\Categoria;
use App\Models\Subcategoria;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class SubcategoriaForm extends Component
{
    public bool $mostrarModal = false;

    public ?int $subcategoriaId = null;

    public ?int $categoriaId = null;

    public string $nombre = '';

    public string $descripcion = '';

    public bool $activo = true;

    public function rules(): array
    {
        return [
            'categoriaId' => ['required', 'exists:categorias,id'],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
        ];
    }

    #[On('abrir-formulario-subcategoria')]
    public function abrir(?int $subcategoriaId = null): void
    {
        $this->resetValidation();
        $this->subcategoriaId = $subcategoriaId;

        if ($subcategoriaId) {
            $subcategoria = Subcategoria::findOrFail($subcategoriaId);
            $this->categoriaId = $subcategoria->categoria_id;
            $this->nombre = $subcategoria->nombre;
            $this->descripcion = $subcategoria->descripcion ?? '';
            $this->activo = (bool) $subcategoria->activo;
        } else {
            $this->reset(['categoriaId', 'nombre', 'descripcion']);
            $this->activo = true;
        }

        $this->mostrarModal = true;
    }

    public function cerrar(): void
    {
        $this->mostrarModal = false;
        $this->resetValidation();
    }

    public function guardar(): void
    {
        $validated = $this->validate();

        $data = [
            'categoria_id' => $validated['categoriaId'],
            'nombre' => $validated['nombre'],
            'descripcion' => $this->descripcion,
            'activo' => $this->activo,
        ];

        if ($this->subcategoriaId) {
            Subcategoria::findOrFail($this->subcategoriaId)->update($data);
        } else {
            Subcategoria::create($data);
        }

        $this->mostrarModal = false;
        $this->dispatch('subcategoria-guardada');
    }

    public function render(): View
    {
        return view('livewire.admin.subcategoria.subcategoria-form', [
            'categorias' => Categoria::where('activo', true)->orderBy('nombre')->get(),
        ]);
    }
}
