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

<div class="flex min-h-screen items-center justify-center overflow-hidden bg-[#ffe0e2] px-5 py-10 dark:bg-[#211222]">
    <div
        x-data="{ flipped: false }"
        class="relative flex w-full flex-col items-center [--ink:#323232] dark:[--ink:#f8f0fa]"
    >
        <div class="z-10 mb-6 flex items-center gap-3 font-sans text-lg font-bold text-[var(--ink)]">
            <button type="button" wire:click="setMode('login')" x-on:click="flipped = false"
                class="transition-opacity" x-bind:class="flipped ? 'opacity-50' : 'opacity-100'">Log in</button>
            <button type="button" wire:click="setMode('register')" x-on:click="flipped = true"
                aria-label="Toggle Login and Sign up"
                class="relative h-7 w-[52px] rounded-full border-2 border-[var(--ink)] bg-[#ffe66d] shadow-[2px_2px_0_var(--ink)] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                <span class="absolute left-[3px] top-[3px] size-4 rounded-full border-2 border-[var(--ink)] bg-white transition-transform duration-500 [transition-timing-function:cubic-bezier(.68,-.55,.265,1.55)]"
                    x-bind:class="flipped ? 'translate-x-6' : ''"></span>
            </button>
            <button type="button" wire:click="setMode('register')" x-on:click="flipped = true"
                class="transition-opacity" x-bind:class="flipped ? 'opacity-100' : 'opacity-50'">Sign up</button>
        </div>

        <div class="relative h-[390px] w-full max-w-[340px] [perspective:1000px]">
            <svg class="pointer-events-none absolute -left-7 -top-5 z-10 h-12 w-12 animate-[auth-float_4s_ease-in-out_infinite]" viewBox="0 0 24 24" fill="#ffd166" stroke="var(--ink)" stroke-width="1.5" aria-hidden="true">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
            </svg>
            <svg class="pointer-events-none absolute -right-5 top-8 z-10 h-8 w-8 animate-[auth-float_4s_ease-in-out_infinite_1s]" viewBox="0 0 24 24" fill="none" stroke="var(--ink)" stroke-width="1.5" stroke-linecap="round" aria-hidden="true">
                <path d="M3 12 C3 5 10 5 16 5 C20 5 21 9 18 12 C15 15 10 13 12 9 C14 5 22 9 21 16" />
            </svg>
            <svg class="pointer-events-none absolute -bottom-5 -right-4 z-10 h-10 w-10 animate-[auth-float_4s_ease-in-out_infinite_2s]" viewBox="0 0 24 24" fill="#06d6a0" stroke="var(--ink)" stroke-width="1.5" aria-hidden="true">
                <path d="M12 2 Q12 12 22 12 Q12 12 12 22 Q12 12 2 12 Q12 12 12 2 Z" />
            </svg>

            <div class="relative h-full w-full [transform-style:preserve-3d] [transition:transform_.8s_cubic-bezier(.4,.2,.2,1)]" x-bind:class="flipped ? '[transform:rotateY(180deg)]' : ''">
                <section class="absolute inset-0 flex flex-col items-center justify-center border-2 border-[var(--ink)] bg-[#fff9e6] p-6 shadow-[4px_4px_0_var(--ink)] [backface-visibility:hidden] [border-radius:8px_24px_8px_24px/24px_8px_24px_8px] dark:bg-[#3b2b40]" style="background-image:repeating-linear-gradient(transparent,transparent 28px,rgba(50,50,50,.08) 28px,rgba(50,50,50,.08) 30px);background-position:0 15px">
                    <h1 class="mb-6 -rotate-3 text-2xl font-black uppercase tracking-wide text-[var(--ink)]">Welcome!</h1>
                    <form wire:submit="login" class="flex w-full flex-col items-center gap-4">
                        <label class="sr-only" for="login-email">Email</label>
                        <input id="login-email" wire:model="email" type="email" autocomplete="email" placeholder="Email" required
                            class="h-11 w-full rounded-[8px_24px_8px_24px/24px_8px_24px_8px] border-2 border-[var(--ink)] bg-white px-4 font-sans text-sm font-semibold text-[#323232] shadow-[3px_3px_0_var(--ink)] outline-none transition focus:border-primary focus:shadow-[4px_4px_0_var(--ink)]">
                        @error('email') <p class="w-full text-xs font-semibold text-danger">{{ $message }}</p> @enderror
                        <label class="sr-only" for="login-password">Password</label>
                        <input id="login-password" wire:model="password" type="password" autocomplete="current-password" placeholder="Password" required
                            class="h-11 w-full rounded-[8px_24px_8px_24px/24px_8px_24px_8px] border-2 border-[var(--ink)] bg-white px-4 font-sans text-sm font-semibold text-[#323232] shadow-[3px_3px_0_var(--ink)] outline-none transition focus:border-primary focus:shadow-[4px_4px_0_var(--ink)]">
                        @error('password') <p class="w-full text-xs font-semibold text-danger">{{ $message }}</p> @enderror
                        <button type="submit" class="mt-2 h-11 min-w-32 -rotate-1 rounded-[16px_5px_16px_5px/5px_16px_5px_16px] border-2 border-[var(--ink)] bg-[#ff6b6b] px-4 font-sans font-black tracking-wide text-[#323232] shadow-[4px_4px_0_var(--ink)] transition hover:-translate-y-0.5 hover:rotate-[-2deg] hover:bg-[#ff5252] active:translate-x-[3px] active:translate-y-[3px] active:shadow-none">Let's Go!</button>
                    </form>
                </section>

                <section class="absolute inset-0 flex flex-col items-center justify-center border-2 border-[var(--ink)] bg-[#e6f0ff] p-6 shadow-[4px_4px_0_var(--ink)] [transform:rotateY(180deg)] [backface-visibility:hidden] [border-radius:24px_8px_24px_8px/8px_24px_8px_24px] dark:bg-[#30233a]" style="background-image:repeating-linear-gradient(transparent,transparent 28px,rgba(50,50,50,.08) 28px,rgba(50,50,50,.08) 30px);background-position:0 15px">
                    <h2 class="mb-4 rotate-2 text-2xl font-black uppercase tracking-wide text-[var(--ink)]">Join Us!</h2>
                    <form wire:submit="register" class="flex w-full flex-col items-center gap-3">
                        <label class="sr-only" for="register-name">Name</label>
                        <input id="register-name" wire:model="name" type="text" autocomplete="name" placeholder="Name" required
                            class="h-10 w-full rounded-[8px_24px_8px_24px/24px_8px_24px_8px] border-2 border-[var(--ink)] bg-white px-4 font-sans text-sm font-semibold text-[#323232] shadow-[3px_3px_0_var(--ink)] outline-none transition focus:border-primary focus:shadow-[4px_4px_0_var(--ink)]">
                        @error('name') <p class="w-full text-xs font-semibold text-danger">{{ $message }}</p> @enderror
                        <label class="sr-only" for="register-email">Email</label>
                        <input id="register-email" wire:model="email" type="email" autocomplete="email" placeholder="Email" required
                            class="h-10 w-full rounded-[8px_24px_8px_24px/24px_8px_24px_8px] border-2 border-[var(--ink)] bg-white px-4 font-sans text-sm font-semibold text-[#323232] shadow-[3px_3px_0_var(--ink)] outline-none transition focus:border-primary focus:shadow-[4px_4px_0_var(--ink)]">
                        @error('email') <p class="w-full text-xs font-semibold text-danger">{{ $message }}</p> @enderror
                        <label class="sr-only" for="register-password">Password</label>
                        <input id="register-password" wire:model="password" type="password" autocomplete="new-password" placeholder="Password (min. 8 karakter)" required
                            class="h-10 w-full rounded-[8px_24px_8px_24px/24px_8px_24px_8px] border-2 border-[var(--ink)] bg-white px-4 font-sans text-sm font-semibold text-[#323232] shadow-[3px_3px_0_var(--ink)] outline-none transition focus:border-primary focus:shadow-[4px_4px_0_var(--ink)]">
                        @error('password') <p class="w-full text-xs font-semibold text-danger">{{ $message }}</p> @enderror
                        <label class="sr-only" for="register-password-confirmation">Confirm password</label>
                        <input id="register-password-confirmation" wire:model="passwordConfirmation" type="password" autocomplete="new-password" placeholder="Confirm password" required
                            class="h-10 w-full rounded-[8px_24px_8px_24px/24px_8px_24px_8px] border-2 border-[var(--ink)] bg-white px-4 font-sans text-sm font-semibold text-[#323232] shadow-[3px_3px_0_var(--ink)] outline-none transition focus:border-primary focus:shadow-[4px_4px_0_var(--ink)]">
                        <button type="submit" class="mt-1 h-10 min-w-32 rotate-1 rounded-[16px_5px_16px_5px/5px_16px_5px_16px] border-2 border-[var(--ink)] bg-[#4ecdc4] px-4 font-sans font-black tracking-wide text-[#323232] shadow-[4px_4px_0_var(--ink)] transition hover:-translate-y-0.5 hover:rotate-2 hover:bg-[#3bbfb6] active:translate-x-[3px] active:translate-y-[3px] active:shadow-none">Confirm!</button>
                    </form>
                </section>
            </div>
        </div>
    </div>
</div>
