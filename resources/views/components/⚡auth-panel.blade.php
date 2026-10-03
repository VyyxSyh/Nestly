<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

new class extends Component
{
    public string $mode = 'login';

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirect(route('dashboard'), navigate: true);
        }
    }

    public function setMode(string $mode): void
    {
        abort_unless(in_array($mode, ['login', 'register'], true), 404);

        $this->resetValidation();
        $this->mode = $mode;
    }

    public function login(): void
    {
        $credentials = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            $this->addError('email', 'Email atau password tidak cocok.');

            return;
        }

        session()->regenerate();
        $this->redirect(route('dashboard'), navigate: true);
    }

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::min(8), 'confirmed:passwordConfirmation'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        Auth::login($user);
        session()->regenerate();
        $this->redirect(route('dashboard'), navigate: true);
    }
};
?>

<div class="auth-doodle doodle-wrapper">
    <input type="checkbox" id="doodle-flip" class="doodle-toggle" aria-label="Toggle Login and Sign up"
        x-on:change="$wire.setMode($event.target.checked ? 'register' : 'login')">

    <div class="doodle-header">
        <button type="button" class="doodle-mode-text login-text" x-on:click="$el.closest('.doodle-wrapper').querySelector('.doodle-toggle').checked = false; $wire.setMode('login')">Log in</button>
        <label class="doodle-switch-label" for="doodle-flip" tabindex="0" aria-hidden="true">
            <span class="doodle-switch-handle"></span>
        </label>
        <button type="button" class="doodle-mode-text signup-text" x-on:click="$el.closest('.doodle-wrapper').querySelector('.doodle-toggle').checked = true; $wire.setMode('register')">Sign up</button>
    </div>

    <div class="doodle-card-scene">
        <svg class="doodle-svg doodle-star" viewBox="0 0 24 24" fill="#ffd166" stroke="var(--ink)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
        </svg>
        <svg class="doodle-svg doodle-sparkle" viewBox="0 0 24 24" fill="#06d6a0" stroke="var(--ink)" stroke-width="1.5" aria-hidden="true">
            <path d="M12 2 Q12 12 22 12 Q12 12 12 22 Q12 12 2 12 Q12 12 12 2 Z"></path>
        </svg>
        <svg class="doodle-svg doodle-swirl" viewBox="0 0 24 24" fill="none" stroke="var(--ink)" stroke-width="1.5" stroke-linecap="round" aria-hidden="true">
            <path d="M3 12 C 3 5 10 5 16 5 C 20 5 21 9 18 12 C 15 15 10 13 12 9 C 14 5 22 9 21 16"></path>
        </svg>

        <div class="doodle-card-inner">
            <section class="doodle-card-front">
                <h1 class="doodle-title">Welcome!</h1>
                <form class="doodle-form" wire:submit="login">
                    <label class="sr-only" for="login-email">Email</label>
                    <input id="login-email" class="doodle-input" wire:model="email" name="email" placeholder="Email" type="email" autocomplete="email" required>
                    @error('email') <p class="doodle-error">{{ $message }}</p> @enderror
                    <label class="sr-only" for="login-password">Password</label>
                    <input id="login-password" class="doodle-input" wire:model="password" name="password" placeholder="Password" type="password" autocomplete="current-password" required>
                    @error('password') <p class="doodle-error">{{ $message }}</p> @enderror
                    <button class="doodle-btn" type="submit">Let's Go!</button>
                </form>
            </section>

            <section class="doodle-card-back">
                <h2 class="doodle-title doodle-title-alt">Join Us!</h2>
                <form class="doodle-form" wire:submit="register">
                    <label class="sr-only" for="register-name">Name</label>
                    <input id="register-name" class="doodle-input" wire:model="name" name="username" placeholder="Name" type="text" autocomplete="name" required>
                    @error('name') <p class="doodle-error">{{ $message }}</p> @enderror
                    <label class="sr-only" for="register-email">Email</label>
                    <input id="register-email" class="doodle-input" wire:model="email" name="email" placeholder="Email" type="email" autocomplete="email" required>
                    @error('email') <p class="doodle-error">{{ $message }}</p> @enderror
                    <label class="sr-only" for="register-password">Password</label>
                    <input id="register-password" class="doodle-input" wire:model="password" name="password" placeholder="Password" type="password" autocomplete="new-password" required>
                    @error('password') <p class="doodle-error">{{ $message }}</p> @enderror
                    <label class="sr-only" for="register-password-confirmation">Confirm password</label>
                    <input id="register-password-confirmation" class="doodle-input" wire:model="passwordConfirmation" name="password_confirmation" placeholder="Confirm Password" type="password" autocomplete="new-password" required>
                    <button class="doodle-btn doodle-btn-alt" type="submit">Confirm!</button>
                </form>
            </section>
        </div>
    </div>
</div>
