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
        <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-indigo-600/20 border border-indigo-500/30">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="#7b95f0" class="w-7 h-7">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.35m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
            </svg>
        </div>
        <div class="text-center">
            <flux:heading>Sublimar Yamer</flux:heading>
            <flux:text class="text-zinc-500">Sistema de Gestión Minorista</flux:text>
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
                <div class="flex justify-between mb-3">
                    <flux:label>Contraseña</flux:label>
                    <x-text-link href="{{ route('password.request') }}" class="text-sm">
                        ¿Olvidaste tu contraseña?
                    </x-text-link>
                </div>
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

    <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400">
        ¿No tienes cuenta?
        <x-text-link href="{{ route('register') }}">Registrarse</x-text-link>
    </div>
</div>
