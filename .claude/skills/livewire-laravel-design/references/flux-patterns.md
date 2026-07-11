# Recetas Flux UI — sistema nuevo (referencia: módulo Producto)

Todos estos snippets son literales de `app/Livewire/Admin/Producto/` y
`resources/views/livewire/admin/producto/`, ya verificados funcionando en el
navegador. Cópialos para el siguiente módulo, adaptando nombres.

## Layout de página

```php
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app.sidebar', ['title' => 'Detalle de Productos'])]
class ProductoIndex extends Component
{
    public function mount(): void
    {
        if (request()->filled('editar')) {
            $this->dispatch('abrir-formulario-producto', productoId: (int) request('editar'));
        } elseif (request()->boolean('crear')) {
            $this->dispatch('abrir-formulario-producto');
        }
    }

    #[On('producto-guardado')]
    #[On('producto-eliminado')]
    public function refrescarKpis(): void
    {
        // Los KPIs se recalculan en render(); este listener solo fuerza el re-render.
    }
}
```

## Header con acción

```blade
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <flux:heading size="xl">Detalle de Productos</flux:heading>
        <flux:subheading>Características completas, costos, precios y rendimiento de ventas.</flux:subheading>
    </div>
    <flux:button variant="primary" icon="plus" wire:click="crear">
        Nuevo Producto
    </flux:button>
</div>
```

```php
public function crear(): void
{
    $this->dispatch('abrir-formulario-producto');
}
```

## Tarjetas KPI

```blade
<flux:card size="sm">
    <flux:subheading>Total Productos</flux:subheading>
    <div class="flex items-end justify-between mt-2">
        <flux:heading size="lg">{{ $totalProductos }}</flux:heading>
        <flux:text size="sm" class="text-green-600 dark:text-green-400">+{{ $nuevosEsteMes }} este mes</flux:text>
    </div>
</flux:card>
```

## Tabla con filtros, orden por columna y paginación

```php
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class ProductoTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'categoria_id')]
    public string $categoriaId = '';

    #[Url]
    public string $ordenar = 'nombre';

    #[Url]
    public string $dir = 'asc';

    protected array $columnasOrdenables = ['nombre', 'sku', 'precio_venta', 'stock'];

    public function updating(string $property): void
    {
        if (in_array($property, ['search', 'categoriaId', 'ordenar', 'dir'])) {
            $this->resetPage();
        }
    }

    public function sort(string $columna): void
    {
        if (! in_array($columna, $this->columnasOrdenables)) return;

        if ($this->ordenar === $columna) {
            $this->dir = $this->dir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->ordenar = $columna;
            $this->dir = 'asc';
        }

        $this->resetPage();
    }

    public function editar(int $id): void
    {
        $this->dispatch('abrir-formulario-producto', productoId: $id);
    }

    public function eliminar(int $id): void
    {
        Producto::findOrFail($id)->delete();
        $this->dispatch('producto-eliminado');
    }
}
```

```blade
<flux:input wire:model.live.debounce.400ms="search" icon="magnifying-glass" placeholder="Nombre o SKU..." class="flex-1 min-w-[200px]" />

<flux:select wire:model.live="categoriaId" placeholder="Categoría" class="min-w-[160px]">
    <flux:select.option value="">Todas</flux:select.option>
    @foreach($categorias as $cat)
        <flux:select.option value="{{ $cat->id }}">{{ $cat->nombre }}</flux:select.option>
    @endforeach
</flux:select>

<flux:table :paginate="$productos">
    <flux:table.columns>
        <flux:table.column>Producto</flux:table.column>
        <flux:table.column sortable :sorted="$ordenar === 'sku'" :direction="$dir" wire:click="sort('sku')">SKU</flux:table.column>
        <flux:table.column align="center">Acciones</flux:table.column>
    </flux:table.columns>

    <flux:table.rows>
        @forelse($productos as $producto)
        <flux:table.row wire:key="producto-{{ $producto->id }}">
            <flux:table.cell>{{ $producto->nombre }}</flux:table.cell>
            <flux:table.cell variant="strong">{{ $producto->sku }}</flux:table.cell>
            <flux:table.cell align="center">
                <flux:button wire:click="editar({{ $producto->id }})" icon="pencil" variant="ghost" size="sm" tooltip="Editar" />
                <flux:button
                    wire:click="eliminar({{ $producto->id }})"
                    wire:confirm="¿Eliminar este producto? Esta acción no se puede deshacer."
                    icon="trash" variant="ghost" size="sm" tooltip="Eliminar"
                />
            </flux:table.cell>
        </flux:table.row>
        @empty
        <flux:table.row>
            <flux:table.cell colspan="3">
                <div class="flex flex-col items-center gap-3 py-16 text-zinc-400">
                    <flux:icon.cube-transparent class="size-12" />
                    <flux:text>No se encontraron resultados.</flux:text>
                </div>
            </flux:table.cell>
        </flux:table.row>
        @endforelse
    </flux:table.rows>
</flux:table>
```

`:paginate="$items"` ya renderiza `<flux:pagination>` solo — no llames
`{{ $items->links() }}` aparte.

## Badge de estado (colores semánticos)

```blade
@php
    $estadoMap = [
        'en_stock' => ['label' => 'En stock', 'color' => 'green'],
        'bajo_stock' => ['label' => 'Bajo stock', 'color' => 'amber'],
        'agotado' => ['label' => 'Agotado', 'color' => 'red'],
    ];
    $e = $estadoMap[$producto->estado] ?? ['label' => $producto->estado, 'color' => 'zinc'];
@endphp
<flux:badge size="sm" :color="$e['color']">{{ $e['label'] }}</flux:badge>
```

Colores disponibles en `flux:badge`: `zinc` (default), `red`, `orange`,
`amber`, `yellow`, `lime`, `green`, `emerald`, `teal`, `cyan`, `sky`, `blue`,
`indigo`, `violet`, `purple`, `fuchsia`, `pink`, `rose`.

## Modal crear/editar dual

```php
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;

class ProductoForm extends Component
{
    use WithFileUploads;

    public bool $mostrarModal = false;
    public ?int $productoId = null;
    public string $nombre = '';
    public string $sku = '';
    // ...resto de campos

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', Rule::unique('productos', 'sku')->ignore($this->productoId)],
            // ...
        ];
    }

    #[On('abrir-formulario-producto')]
    public function abrir(?int $productoId = null): void
    {
        $this->resetValidation();
        $this->productoId = $productoId;

        if ($productoId) {
            $producto = Producto::findOrFail($productoId);
            $this->nombre = $producto->nombre;
            // ...cargar resto de campos
        } else {
            $this->reset(['nombre', 'sku' /* ... */]);
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
        // ...mapear a columnas snake_case y guardar
        $this->mostrarModal = false;
        $this->dispatch('producto-guardado');
    }
}
```

```blade
<flux:modal wire:model="mostrarModal" class="max-w-3xl">
    <form wire:submit="guardar" class="space-y-6">
        <div>
            <flux:heading size="lg">{{ $productoId ? 'Editar Producto' : 'Nuevo Producto' }}</flux:heading>
            <flux:subheading>Completa la información del producto.</flux:subheading>
        </div>

        <flux:input wire:model="nombre" label="Nombre del Producto" required />
        <flux:select wire:model="categoriaId" label="Categoría" placeholder="Seleccionar..." required>
            @foreach($categorias as $cat)
                <flux:select.option value="{{ $cat->id }}">{{ $cat->nombre }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:textarea wire:model="descripcion" label="Descripción" rows="3" />
        <flux:checkbox wire:model="activo" label="Producto activo" description="Texto de ayuda opcional." />

        <flux:button type="submit" variant="primary">Guardar</flux:button>
        <flux:button type="button" variant="ghost" wire:click="cerrar">Cancelar</flux:button>
    </form>
</flux:modal>
```

`wire:model="mostrarModal"` en `<flux:modal>` es todo lo que hace falta para
que abra/cierre — Flux entabla el booleano automáticamente. `<flux:input>` /
`<flux:select>` / `<flux:textarea>` muestran el error de validación solo si
`rules()` tiene una regla para ese `wire:model` — no agregues `@error` manual.

## Subida de archivo (Flux no tiene componente propio)

```blade
@if($imagen)
    <img src="{{ $imagen->temporaryUrl() }}" class="w-full h-32 object-cover rounded-lg border border-zinc-200 dark:border-white/10">
@elseif($imagenActual)
    <img src="{{ asset('storage/' . $imagenActual) }}" class="w-full h-32 object-cover rounded-lg border border-zinc-200 dark:border-white/10">
@endif
<input type="file" wire:model="imagen" accept="image/*"
       class="w-full text-sm text-zinc-600 dark:text-zinc-300 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-zinc-800/5 dark:file:bg-white/10 file:text-zinc-800 dark:file:text-white file:font-medium hover:file:bg-zinc-800/10 dark:hover:file:bg-white/20 transition-all">
@error('imagen') <flux:text size="sm" class="text-red-600 dark:text-red-400">{{ $message }}</flux:text> @enderror
```

## Callout de éxito/error

```blade
@if($mensaje)
    <flux:callout icon="check-circle" variant="success" heading="{{ $mensaje }}" class="mb-6" />
@endif
```

`variant`: `success` (verde), `danger` (rojo), `warning` (amarillo),
`secondary` (zinc), o usa `color="..."` directamente para cualquier color de
la lista de badges.
