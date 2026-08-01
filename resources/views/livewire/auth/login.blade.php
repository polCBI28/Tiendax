<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false));
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<div class="flex flex-col items-center gap-6 w-full max-w-sm mx-auto">
    {{-- Brand --}}
    <div class="flex flex-col items-center gap-3">
        <x-brand-logo size="lg" />
        <div class="text-center">
            <flux:heading>Sublimar Yamer</flux:heading>
            <flux:text>Sistema de Gestión Minorista</flux:text>
        </div>
    </div>

    {{-- Session status --}}
    <x-auth-session-status class="text-center" :status="session('status')" />

    {{-- Card --}}
    <flux:card class="w-full space-y-6">
        <div class="space-y-1">
            <flux:heading size="lg">Iniciar sesión</flux:heading>
            <flux:subheading>Ingresa tus credenciales para acceder al sistema</flux:subheading>
        </div>

        <flux:separator />

        <form wire:submit="login" class="space-y-5">
            <flux:input wire:model="email" label="Correo electrónico" type="email" name="email" required autofocus autocomplete="email" placeholder="correo@ejemplo.com" />

            <flux:field>
                <flux:label>Contraseña</flux:label>
                <flux:input wire:model="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                <flux:error name="password" />
            </flux:field>

            <flux:checkbox wire:model="remember" label="Recordarme en este equipo" />

            <flux:button type="submit" variant="primary" class="w-full">
                <span wire:loading.remove wire:target="login">Iniciar sesión</span>
                <span wire:loading wire:target="login" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                    Verificando...
                </span>
            </flux:button>
        </form>
    </flux:card>
</div>
