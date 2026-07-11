<?php

namespace App\Livewire\Admin\Cliente;

use App\Models\Cliente;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class ClienteForm extends Component
{
    public bool $mostrarModal = false;

    public ?int $clienteId = null;

    public string $nombre = '';

    public string $documento = '';

    public string $telefono = '';

    public string $email = '';

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'documento' => ['nullable', 'string', 'max:20'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
        ];
    }

    #[On('abrir-formulario-cliente')]
    public function abrir(?int $clienteId = null): void
    {
        $this->resetValidation();
        $this->clienteId = $clienteId;

        if ($clienteId) {
            $cliente = Cliente::findOrFail($clienteId);
            $this->nombre = $cliente->nombre;
            $this->documento = $cliente->documento ?? '';
            $this->telefono = $cliente->telefono ?? '';
            $this->email = $cliente->email ?? '';
        } else {
            $this->reset(['nombre', 'documento', 'telefono', 'email']);
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

        if ($this->clienteId) {
            Cliente::findOrFail($this->clienteId)->update($validated);
        } else {
            Cliente::create($validated);
        }

        $this->mostrarModal = false;
        $this->dispatch('cliente-guardado');
    }

    public function render(): View
    {
        return view('livewire.admin.cliente.cliente-form');
    }
}
