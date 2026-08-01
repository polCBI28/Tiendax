<?php

use App\Models\Configuracion;
use App\Services\LogoProcessor;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Layout('components.layouts.app.sidebar', ['title' => 'Logo'])] class extends Component {
    use WithFileUploads;

    public $logo = null;

    public bool $quitarFondo = false;

    public ?string $logoActual = null;

    public ?int $logoVersion = null;

    public function mount(): void
    {
        $configuracion = Configuracion::actual();
        $this->quitarFondo = $configuracion->logo_quitar_fondo;
        $this->logoActual = $configuracion->logo_path;
        $this->logoVersion = $this->logoActual ? Storage::disk('public')->lastModified($this->logoActual) : null;
    }

    public function guardar(): void
    {
        $this->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
        ]);

        $contenido = (new LogoProcessor)->procesar($this->logo->getRealPath(), $this->quitarFondo);

        $configuracion = Configuracion::actual();

        if ($configuracion->logo_path) {
            Storage::disk('public')->delete($configuracion->logo_path);
        }

        $ruta = 'logo/logo-'.time().'.png';
        Storage::disk('public')->put($ruta, $contenido);

        $configuracion->update([
            'logo_path' => $ruta,
            'logo_quitar_fondo' => $this->quitarFondo,
        ]);
        Configuracion::olvidarCache();

        $this->logoActual = $ruta;
        $this->logoVersion = Storage::disk('public')->lastModified($ruta);
        $this->reset('logo');

        Flux::toast(text: 'Logo actualizado correctamente.', variant: 'success');
    }

    public function quitarLogo(): void
    {
        $configuracion = Configuracion::actual();

        if ($configuracion->logo_path) {
            Storage::disk('public')->delete($configuracion->logo_path);
        }

        $configuracion->update(['logo_path' => null]);
        Configuracion::olvidarCache();

        $this->logoActual = null;
        $this->logoVersion = null;

        Flux::toast(text: 'Se restauró el logo predeterminado.', variant: 'success');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout heading="Logo" subheading="Personaliza el logo que se muestra en el inicio de sesión y en la barra lateral.">
        <div class="space-y-6">
            <div>
                <flux:text class="mb-2">Logo actual</flux:text>
                <div class="flex items-center justify-center h-24 w-full rounded-lg border border-dashed border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-white/5 p-4">
                    @if($logoActual)
                        <img src="{{ asset('storage/'.$logoActual) }}?v={{ $logoVersion }}" alt="Logo actual" class="max-h-full max-w-full object-contain">
                    @else
                        <div class="flex items-center gap-2">
                            <span class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground font-bold text-sm">SY</span>
                            <span class="font-semibold text-sm">Sublimar Yamer</span>
                        </div>
                    @endif
                </div>
            </div>

            <form wire:submit="guardar" class="space-y-6">
                <div>
                    <input type="file" wire:model="logo" id="logo-input" accept="image/png,image/jpeg,image/webp" class="sr-only" />
                    <label for="logo-input" class="inline-flex items-center gap-2 cursor-pointer rounded-lg border border-zinc-300 dark:border-zinc-600 px-4 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-white/5">
                        <flux:icon.arrow-up-tray variant="micro" />
                        Seleccionar imagen
                    </label>

                    <flux:text size="sm" class="mt-2 text-zinc-400">Formatos: PNG, JPG o WEBP. Máximo 4 MB.</flux:text>

                    @if($logo)
                        <flux:text size="sm" class="mt-1 text-zinc-500">{{ $logo->getClientOriginalName() }}</flux:text>
                    @endif
                    @error('logo') <flux:text size="sm" class="text-red-600 dark:text-red-400 mt-1">{{ $message }}</flux:text> @enderror
                </div>

                <flux:checkbox
                    wire:model="quitarFondo"
                    label="Quitar fondo automáticamente"
                    description="Si tu imagen tiene un fondo de color sólido (blanco, gris, etc.), lo detectamos y lo volvemos transparente. El logo también se ajusta de tamaño solo si la imagen es muy grande."
                />

                <div class="flex gap-3">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="logo,guardar">
                        <span wire:loading.remove wire:target="logo,guardar">Guardar logo</span>
                        <span wire:loading wire:target="logo,guardar">Procesando...</span>
                    </flux:button>
                    @if($logoActual)
                        <flux:button type="button" variant="ghost" wire:click="quitarLogo" wire:confirm="¿Restaurar el logo predeterminado?">
                            Quitar logo
                        </flux:button>
                    @endif
                </div>
            </form>
        </div>
    </x-settings.layout>
</section>
