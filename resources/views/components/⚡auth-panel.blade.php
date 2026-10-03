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
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers(), 'confirmed:passwordConfirmation'],
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
                <form class="doodle-form" wire:submit="login" x-data="{ email: '', password: '', get emailValid() { return this.email.includes('@') }, get passwordValid() { return this.password.length >= 8 && /[A-Z]/.test(this.password) && /[a-z]/.test(this.password) && /[0-9]/.test(this.password) } }">
                    <label class="sr-only" for="login-email">Email</label>
                    <input id="login-email" class="doodle-input" wire:model="email" x-model="email" x-on:focus="$el.classList.add('is-focused')" x-on:blur="$el.classList.remove('is-focused')" x-bind:class="emailValid ? 'is-valid' : 'is-invalid'" name="email" placeholder="Email" type="email" autocomplete="email" required>
                    @error('email') <p class="doodle-error">{{ $message }}</p> @enderror
                    <label class="sr-only" for="login-password">Password</label>
                    <div class="doodle-password-wrap" x-data="{ visible: false }">
                        <input id="login-password" class="doodle-input" wire:model="password" x-model="password" x-on:focus="$el.classList.add('is-focused')" x-on:blur="$el.classList.remove('is-focused')" x-bind:class="passwordValid ? 'is-valid' : 'is-invalid'" name="password" placeholder="Password" x-bind:type="visible ? 'text' : 'password'" autocomplete="current-password" required>
                        <button class="doodle-password-toggle" type="button" x-on:click="visible = !visible" x-bind:aria-label="visible ? 'Hide password' : 'Show password'" x-bind:title="visible ? 'Hide password' : 'Show password'">
                            <svg x-show="!visible" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-cloak x-show="visible" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a15 15 0 0 1-3 3.8M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7c1.2 0 2.3-.2 3.3-.7"/></svg>
                        </button>
                    </div>
                    @error('password') <p class="doodle-error">{{ $message }}</p> @enderror
                    <button class="doodle-btn" type="submit" x-bind:disabled="!emailValid || !passwordValid">Let's Go!</button>
                </form>
            </section>

            <section class="doodle-card-back">
                <h2 class="doodle-title doodle-title-alt">Join Us!</h2>
                <form class="doodle-form" wire:submit="register" x-data="{ email: '', password: '', confirmation: '', get emailValid() { return this.email.includes('@') }, get passwordValid() { return this.password.length >= 8 && /[A-Z]/.test(this.password) && /[a-z]/.test(this.password) && /[0-9]/.test(this.password) }, get confirmationValid() { return this.confirmation.length > 0 && this.confirmation === this.password } }">
                    <label class="sr-only" for="register-name">Name</label>
                    <input id="register-name" class="doodle-input" wire:model="name" name="username" placeholder="Name" type="text" autocomplete="name" required>
                    @error('name') <p class="doodle-error">{{ $message }}</p> @enderror
                    <label class="sr-only" for="register-email">Email</label>
                    <input id="register-email" class="doodle-input" wire:model="email" x-model="email" x-on:focus="$el.classList.add('is-focused')" x-on:blur="$el.classList.remove('is-focused')" x-bind:class="emailValid ? 'is-valid' : 'is-invalid'" name="email" placeholder="Email" type="email" autocomplete="email" required>
                    @error('email') <p class="doodle-error">{{ $message }}</p> @enderror
                    <label class="sr-only" for="register-password">Password</label>
                    <div class="doodle-password-wrap" x-data="{ visible: false }">
                        <input id="register-password" class="doodle-input" wire:model="password" x-model="password" x-on:focus="$el.classList.add('is-focused')" x-on:blur="$el.classList.remove('is-focused')" x-bind:class="passwordValid ? 'is-valid' : 'is-invalid'" name="password" placeholder="Password" x-bind:type="visible ? 'text' : 'password'" autocomplete="new-password" aria-describedby="password-requirements" required>
                        <button class="doodle-password-toggle" type="button" x-on:click="visible = !visible" x-bind:aria-label="visible ? 'Hide password' : 'Show password'" x-bind:title="visible ? 'Hide password' : 'Show password'">
                            <svg x-show="!visible" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-cloak x-show="visible" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a15 15 0 0 1-3 3.8M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7c1.2 0 2.3-.2 3.3-.7"/></svg>
                        </button>
                    </div>
                    <p id="password-requirements" class="doodle-password-hint" aria-live="polite">Min. 8 karakter, huruf besar, huruf kecil, dan angka.</p>
                    @error('password') <p class="doodle-error">{{ $message }}</p> @enderror
                    <label class="sr-only" for="register-password-confirmation">Confirm password</label>
                    <div class="doodle-password-wrap" x-data="{ visible: false }">
                        <input id="register-password-confirmation" class="doodle-input" wire:model="passwordConfirmation" x-model="confirmation" x-on:focus="$el.classList.add('is-focused')" x-on:blur="$el.classList.remove('is-focused')" x-bind:class="confirmationValid ? 'is-valid' : 'is-invalid'" name="password_confirmation" placeholder="Confirm Password" x-bind:type="visible ? 'text' : 'password'" autocomplete="new-password" required>
                        <button class="doodle-password-toggle" type="button" x-on:click="visible = !visible" x-bind:aria-label="visible ? 'Hide password' : 'Show password'" x-bind:title="visible ? 'Hide password' : 'Show password'">
                            <svg x-show="!visible" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-cloak x-show="visible" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a15 15 0 0 1-3 3.8M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7c1.2 0 2.3-.2 3.3-.7"/></svg>
                        </button>
                    </div>
                    <button class="doodle-btn doodle-btn-alt" type="submit" x-bind:disabled="!passwordValid || !confirmationValid">Confirm!</button>
                </form>
            </section>
        </div>
    </div>
</div>
