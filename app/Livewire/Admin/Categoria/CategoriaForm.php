<?php

namespace App\Livewire\Admin\Categoria;

use App\Models\Categoria;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class CategoriaForm extends Component
{
    use WithFileUploads;

    public bool $mostrarModal = false;

    public ?int $categoriaId = null;

    public string $nombre = '';

    public string $descripcion = '';

    public string $icono = 'category';

    public bool $activo = true;

    public $imagen = null;

    public ?string $imagenActual = null;

    /** @var array<string, array<int, string>> */
    public array $iconos = [
        'Ropa y moda' => ['checkroom', 'dry_cleaning', 'style', 'apparel', 'laundry', 'diamond', 'watch', 'king_bed'],
        'Alimentos' => ['restaurant', 'local_cafe', 'lunch_dining', 'fastfood', 'local_pizza', 'bakery_dining', 'icecream', 'liquor'],
        'Tecnología' => ['devices', 'smartphone', 'laptop', 'headphones', 'keyboard', 'mouse', 'tablet', 'monitor'],
        'Hogar' => ['home', 'chair', 'bed', 'kitchen', 'cleaning_services', 'outdoor_grill', 'light', 'bathtub'],
        'Salud y belleza' => ['spa', 'face', 'medical_services', 'local_pharmacy', 'fitness_center', 'self_improvement', 'health_and_beauty', 'sanitizer'],
        'Deportes' => ['sports_soccer', 'sports_basketball', 'sports_tennis', 'directions_bike', 'pool', 'hiking', 'sports_esports', 'skateboarding'],
        'Juguetes' => ['toys', 'child_care', 'sports_esports', 'casino', 'palette', 'music_note', 'movie', 'photo_camera'],
        'Otros' => ['category', 'sell', 'local_offer', 'redeem', 'card_giftcard', 'inventory_2', 'shopping_bag', 'store'],
    ];

    public string $buscarIcono = '';

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'icono' => ['nullable', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string'],
            'imagen' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function getIconosFiltradosProperty(): array
    {
        if ($this->buscarIcono === '') {
            return $this->iconos;
        }

        $q = strtolower($this->buscarIcono);

        return collect($this->iconos)
            ->map(fn ($lista) => array_values(array_filter($lista, fn ($icon) => str_contains($icon, $q))))
            ->filter(fn ($lista) => count($lista) > 0)
            ->all();
    }

    public function seleccionarIcono(string $icon): void
    {
        $this->icono = $icon;
    }

    #[On('abrir-formulario-categoria')]
    public function abrir(?int $categoriaId = null): void
    {
        $this->resetValidation();
        $this->categoriaId = $categoriaId;
        $this->imagen = null;
        $this->buscarIcono = '';

        if ($categoriaId) {
            $categoria = Categoria::findOrFail($categoriaId);
            $this->nombre = $categoria->nombre;
            $this->descripcion = $categoria->descripcion ?? '';
            $this->icono = $categoria->icono ?? 'category';
            $this->activo = (bool) $categoria->activo;
            $this->imagenActual = $categoria->imagen;
        } else {
            $this->reset(['nombre', 'descripcion', 'imagenActual']);
            $this->icono = 'category';
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
            'nombre' => $validated['nombre'],
            'icono' => $this->icono,
            'descripcion' => $this->descripcion,
            'activo' => $this->activo,
        ];

        if ($this->imagen) {
            $data['imagen'] = $this->imagen->store('categorias', 'public');
        }

        if ($this->categoriaId) {
            Categoria::findOrFail($this->categoriaId)->update($data);
        } else {
            Categoria::create($data);
        }

        $this->mostrarModal = false;
        $this->dispatch('categoria-guardada');
    }

    public function render(): View
    {
        return view('livewire.admin.categoria.categoria-form');
    }
}
