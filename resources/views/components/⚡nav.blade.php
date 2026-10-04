<?php

use App\Models\Schedule;
use App\Models\Task;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public string $themeMode = 'dark';

    public string $language = 'id';

    public string $themeColor = 'pink';

    public string $titleFont = 'sans_serif';

    public string $descriptionFont = 'sans_serif';

    public string $name = '';

    public string $nickname = '';

    public string $newPassword = '';

    public string $passwordConfirmation = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->themeMode = $user->theme_mode;
        $this->language = $user->language;
        $this->themeColor = $user->theme_color;
        $this->titleFont = $user->title_font;
        $this->descriptionFont = $user->description_font;
        $this->name = $user->name;
        $this->nickname = $user->nickname ?? '';
    }

    public function boot(): void
    {
        app()->setLocale(auth()->user()->language);
    }

    public function updatedThemeMode(string $value): void
    {
        $this->validate(['themeMode' => ['required', 'in:dark,light']]);
        auth()->user()->update(['theme_mode' => $value]);
        $this->dispatch('theme-mode-updated', dark: $value === 'dark');
    }

    public function updatedLanguage(string $value): void
    {
        $this->validate(['language' => ['required', 'in:id,en']]);
        auth()->user()->update(['language' => $value]);
        app()->setLocale($value);
    }

    public function updatedThemeColor(string $value): void
    {
        $this->validate(['themeColor' => ['required', 'in:pink,blue']]);
        auth()->user()->update(['theme_color' => $value]);
    }

    public function updatedTitleFont(string $value): void
    {
        $this->validate(['titleFont' => ['required', 'in:sans_serif,handwritting']]);
        auth()->user()->update(['title_font' => $value]);
    }

    public function updatedDescriptionFont(string $value): void
    {
        $this->validate(['descriptionFont' => ['required', 'in:sans_serif,handwritting']]);
        auth()->user()->update(['description_font' => $value]);
    }

    public function updatedName(string $value): void
    {
        $this->validate(['name' => ['required', 'string', 'max:255']]);
        auth()->user()->update(['name' => trim($value)]);
        $this->dispatch('profile-updated');
    }

    public function updatedNickname(string $value): void
    {
        $this->validate(['nickname' => ['nullable', 'string', 'max:255']]);
        auth()->user()->update(['nickname' => trim($value) ?: null]);
        $this->dispatch('profile-updated');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'newPassword' => ['required', 'string', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
            'passwordConfirmation' => ['required', 'same:newPassword'],
        ], [
            'passwordConfirmation.required' => 'Konfirmasi password wajib diisi.',
            'passwordConfirmation.same' => 'Konfirmasi password tidak cocok.',
        ]);
        auth()->user()->update(['password' => $this->newPassword]);
        $this->reset('newPassword', 'passwordConfirmation');
        session()->flash('password-updated', true);
    }

    #[On('badges-updated')]
    public function refreshBadges(): void {}

    public function with(): array
    {
        $hariIni = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][now()->dayOfWeekIso - 1];

        return [
            'badges' => [
                'tasks' => Task::where('user_id', auth()->id())->where('progress', '<', 100)->count(),
                'schedules' => Schedule::where('user_id', auth()->id())->where('day', $hariIni)->count(),
            ],
            'menus' => [
                ['route' => 'dashboard', 'label' => 'Home', 'icon' => 'fa-house', 'url' => '/'],
                ['route' => 'tasks', 'label' => 'Task', 'icon' => 'fa-list-check', 'url' => '/tasks'],
                ['route' => 'schedules', 'label' => 'Schedule', 'icon' => 'fa-calendar-days', 'url' => '/schedules'],
                ['route' => 'subjects', 'label' => 'Subjects', 'icon' => 'fa-book', 'url' => '/subjects'],
                ['route' => 'finance', 'label' => 'Finance', 'icon' => 'fa-wallet', 'url' => '/finance'],
            ],
        ];
    }
};
?>

<div
    x-data="{
        path: '',
        scrolled: false,
        accountOpen: false,
        desktopIndicator: { left: 0, width: 0, opacity: 0 },
        mobileIndicator: { left: 0, width: 0, opacity: 0 },
        norm(p) { return (p.replace(/\/+$/, '') || '/').toLowerCase() },
        is(url) {
            const urlPath = this.norm(new URL(url, location.origin).pathname);
            return urlPath === this.path;
        },
        updateIndicators() {
            this.path = this.norm(location.pathname);
            const run = () => {
                this.$nextTick(() => {
                    requestAnimationFrame(() => {
                        // Desktop
                        if (this.$refs.desktopNav) {
                            const activeEl = Array.from(this.$refs.desktopNav.querySelectorAll('a')).find(a => {
                                const href = a.getAttribute('href');
                                return href && this.is(href);
                            });
                            if (activeEl) {
                                const containerRect = this.$refs.desktopNav.getBoundingClientRect();
                                const elRect = activeEl.getBoundingClientRect();
                                this.desktopIndicator = {
                                    left: elRect.left - containerRect.left,
                                    width: elRect.width,
                                    opacity: 1
                                };
                            }
                        }
                        // Mobile
                        if (this.$refs.mobileNav) {
                            const activeEl = Array.from(this.$refs.mobileNav.querySelectorAll('a')).find(a => {
                                const href = a.getAttribute('href');
                                return href && this.is(href);
                            });
                            if (activeEl) {
                                const containerRect = this.$refs.mobileNav.getBoundingClientRect();
                                const elRect = activeEl.getBoundingClientRect();
                                this.mobileIndicator = {
                                    left: elRect.left - containerRect.left,
                                    width: elRect.width,
                                    opacity: 1
                                };
                            }
                        }
                    });
                });
            };

            // Run multiple times during CSS transition to catch final width
            run();
            setTimeout(run, 100);
            setTimeout(run, 300);
        },
        init() {
            this.path = this.norm(location.pathname);
            this.scrolled = window.scrollY > 8;
            this.updateIndicators();
            window.addEventListener('resize', () => this.updateIndicators());
        },
    }"
    x-init="
        document.documentElement.classList.toggle('dark', $wire.themeMode === 'dark');
        localStorage.setItem('theme', $wire.themeMode);
        window.currentThemeMode = $wire.themeMode;
        document.documentElement.dataset.titleFont = $wire.titleFont;
        document.documentElement.dataset.descriptionFont = $wire.descriptionFont;
        document.documentElement.lang = $wire.language;
    "
    x-effect="
        document.documentElement.dataset.titleFont = $wire.titleFont;
        document.documentElement.dataset.descriptionFont = $wire.descriptionFont;
        document.documentElement.lang = $wire.language;
    "
    x-on:scroll.window.passive="scrolled = window.scrollY > 8"
    x-on:livewire:navigated.window="
        scrolled = window.scrollY > 8;
        updateIndicators();
    "
    x-on:keydown.escape.window="accountOpen = false"
    x-on:click.outside="accountOpen = false"
    x-on:theme-changed.window="$wire.set('themeMode', $event.detail ? 'dark' : 'light')"
    x-on:theme-mode-updated.window="
        document.documentElement.classList.toggle('dark', $event.detail.dark);
        localStorage.setItem('theme', $event.detail.dark ? 'dark' : 'light');
        window.currentThemeMode = $event.detail.dark ? 'dark' : 'light';
    "
>
    {{-- Desktop & Tablet Bottom Nav --}}
    <nav class="hidden sm:flex fixed bottom-3 lg:bottom-4 inset-x-0 z-50 justify-center px-4">
        <div class="grid grid-cols-[1fr_auto_1fr] items-center w-full max-w-7xl bg-surface/10 backdrop-blur-xs border border-border/70 rounded-full px-5 py-2.5 lg:px-6 lg:py-3 shadow-lg">

            {{-- Kiri: logo + teks --}}
            <div class="flex items-center gap-2 font-bold text-sm md:text-base text-text">
                <img src="{{ asset('logo.png') }}" alt="Nestly" class="h-7 w-auto">
                Nestly
            </div>

            {{-- Tengah: menu berlabel --}}
            <div x-ref="desktopNav" class="relative flex items-center gap-0.5 md:gap-1 lg:gap-2">
                {{-- Sliding Indicator Pill Desktop --}}
                <div class="absolute h-full border-2 border-primary rounded-full transition-all duration-300 ease-out pointer-events-none"
                     :style="`left: ${desktopIndicator.left}px; width: ${desktopIndicator.width}px; opacity: ${desktopIndicator.opacity};`"
                     x-cloak></div>

                @foreach ($menus as $menu)
                    <a href="{{ $menu['url'] }}" wire:navigate
                       @click="path = norm('{{ $menu['url'] }}'); updateIndicators();"
                       class="relative flex items-center rounded-full px-3 py-2 md:px-4 text-sm font-medium transition-colors duration-300 z-10"
                       :class="is('{{ $menu['url'] }}') ? 'text-primary font-bold' : 'text-text-muted hover:text-text'">
                        <span class="inline-flex overflow-hidden transition-all duration-300 ease-out"
                              :class="is('{{ $menu['url'] }}') ? 'max-w-5 mr-2' : 'max-w-0 mr-0'">
                            <i class="fa-solid {{ $menu['icon'] }} shrink-0"></i>
                        </span>
                        {{ $menu['label'] }}
                        @if (($badges[$menu['route']] ?? 0) > 0)
                            <span class="absolute -top-1 -right-1 {{ $menu['route'] === 'tasks' ? 'bg-danger' : 'bg-primary' }} text-white text-[10px] rounded-full min-w-4 h-4 px-1 flex items-center justify-center font-bold z-20">
                                {{ $badges[$menu['route']] > 99 ? '99+' : $badges[$menu['route']] }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>

            {{-- Kanan: akun --}}
            <div class="flex items-center justify-end gap-2">
                <button type="button" aria-label="Account" title="Account" x-on:click="accountOpen = !accountOpen"
                    class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-full text-text-muted transition hover:bg-primary/10 hover:text-primary focus-visible:outline-2 focus-visible:outline-primary">
                    <i class="fa-regular fa-user" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </nav>

    {{-- Mobile Top Bar --}}
    <div class="sm:hidden fixed top-0 inset-x-0 z-50 flex items-center justify-between rounded-b-2xl px-4 py-3 bg-surface/10 backdrop-blur-xs border-b border-border/70 shadow-md transition-all duration-300">
        <div class="flex items-center gap-2 font-bold text-text">
            <img src="{{ asset('logo.png') }}" alt="Nestly" class="h-7 w-auto">
            Nestly
        </div>
        <div class="flex items-center gap-3">
            <button type="button" aria-label="Account" title="Account" x-on:click="accountOpen = !accountOpen"
                class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-full text-text-muted transition hover:bg-primary/10 hover:text-primary focus-visible:outline-2 focus-visible:outline-primary">
                <i class="fa-regular fa-user" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    {{-- Account settings popup --}}
    <section x-cloak x-show="accountOpen" x-transition.opacity
        class="account-popup fixed top-1/2 left-1/2 z-[60] w-[min(22rem,calc(100vw-1.5rem))] -translate-x-1/2 -translate-y-1/2 rounded-2xl border border-border/70 bg-surface/20 p-4 text-text shadow-xl backdrop-blur-sm"
        aria-label="Account settings">
        <header class="mb-3 border-b border-border/60 pb-2">
            <h2 class="text-lg font-bold">Account</h2>
        </header>

        <div class="max-h-[70vh] space-y-3 overflow-y-auto px-2.5 sm:px-3">
            <section class="space-y-2">
                <h3 class="font-semibold">Profil</h3>
                <label class="block text-sm text-text-muted">Nama panggilan
                    <input type="text" wire:model.live.debounce.400ms="nickname" placeholder="Nama panggilan" class="mt-1 w-full rounded-lg border border-border/70 bg-surface/10 px-3 py-2 text-text placeholder:text-text-muted">
                    @error('nickname') <span class="text-danger">{{ $message }}</span> @enderror
                </label>
                <label class="block text-sm text-text-muted">Nama lengkap
                    <input type="text" wire:model.live.debounce.400ms="name" placeholder="Nama lengkap" class="mt-1 w-full rounded-lg border border-border/70 bg-surface/10 px-3 py-2 text-text placeholder:text-text-muted">
                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                </label>
            </section>

            <section class="flex items-center justify-between gap-3 border-t border-border/60 pt-3">
                <div>
                    <h3 class="font-semibold">Mode tampilan</h3>
                    <p class="text-sm text-text-muted">Light / Dark</p>
                </div>
                <label class="flex items-center gap-2 text-sm">
                    <span>Light</span>
                    <x-theme-toggle />
                    <span>Dark</span>
                </label>
            </section>

            <section class="grid grid-cols-2 gap-2 border-t border-border/60 pt-3">
                <label class="text-sm text-text-muted">Bahasa
                    <select wire:model.live="language" class="mt-1 w-full rounded-lg border border-border/70 bg-surface/10 px-2 py-2 text-text">
                        <option value="id">Indonesia</option><option value="en">English</option>
                    </select>
                </label>
                <label class="text-sm text-text-muted">Theme
                    <select wire:model.live="themeColor" class="mt-1 w-full rounded-lg border border-border/70 bg-surface/10 px-2 py-2 text-text">
                        <option value="pink">Pink</option><option value="blue">Blue</option>
                    </select>
                </label>
            </section>

            <section class="grid grid-cols-2 gap-2 border-t border-border/60 pt-3">
                <label class="text-sm text-text-muted">Font judul
                    <select wire:model.live="titleFont" class="mt-1 w-full rounded-lg border border-border/70 bg-surface/10 px-2 py-2 text-text">
                        <option value="sans_serif">Sans-serif</option><option value="handwritting">Handwriting</option>
                    </select>
                </label>
                <label class="text-sm text-text-muted">Font deskripsi
                    <select wire:model.live="descriptionFont" class="mt-1 w-full rounded-lg border border-border/70 bg-surface/10 px-2 py-2 text-text">
                        <option value="sans_serif">Sans-serif</option><option value="handwritting">Handwriting</option>
                    </select>
                </label>
            </section>

            <form wire:submit="updatePassword" class="space-y-2">
                <input type="password" wire:model="newPassword" placeholder="password" autocomplete="new-password" class="w-full rounded-lg border border-border/70 bg-surface/10 px-3 py-2 text-text placeholder:text-text-muted">
                @error('newPassword') <span class="block text-sm text-danger">{{ $message }}</span> @enderror
                <div class="flex items-center gap-2 rounded-lg border border-border/70 bg-surface/10 pr-1.5 focus-within:border-info">
                    <input type="password" wire:model="passwordConfirmation" placeholder="Verifikasi password" autocomplete="new-password" class="min-w-0 flex-1 bg-transparent px-3 py-2 text-text outline-none placeholder:text-text-muted">
                    <button type="submit" aria-label="Ganti password" title="Ganti password" class="flex h-8 w-8 shrink-0 cursor-pointer items-center justify-center rounded-full text-text-muted transition hover:bg-primary/10 hover:text-primary focus-visible:outline-2 focus-visible:outline-primary">
                        <i class="fa-solid fa-check" aria-hidden="true"></i>
                    </button>
                </div>
                @error('passwordConfirmation') <span class="block text-sm text-danger">{{ $message }}</span> @enderror
                @if (session()->has('password-updated')) <span class="block text-sm text-success">Password diperbarui.</span> @endif
            </form>

            <form method="POST" action="{{ route('logout') }}" class="border-t border-border/60 pt-3">
                @csrf
                <button type="submit" class="w-full rounded-lg bg-danger px-3 py-2 font-semibold text-white transition hover:brightness-110">
                    <i class="fa-solid fa-arrow-right-from-bracket mr-2" aria-hidden="true"></i>Log out
                </button>
            </form>
        </div>
    </section>

    {{-- Mobile Bottom Nav (pill melayang, lebar dibagi otomatis, label muncul di menu aktif) --}}
    <nav class="sm:hidden fixed bottom-3 inset-x-2 min-[360px]:inset-x-3 z-50 bg-surface/20 backdrop-blur-xs border border-border/70 rounded-full shadow-lg px-1.5 min-[360px]:px-2 py-1.5">
        <div x-ref="mobileNav" class="relative flex items-center w-full">
            {{-- Sliding Indicator Pill Mobile --}}
                <div class="absolute h-10 border-2 border-primary rounded-full transition-all duration-300 ease-out pointer-events-none"
                 :style="`left: ${mobileIndicator.left}px; width: ${mobileIndicator.width}px; opacity: ${mobileIndicator.opacity}; transition: left 0.35s cubic-bezier(0.34, 1.56, 0.64, 1), width 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);`"
                 x-cloak></div>

            @foreach ($menus as $menu)
                <a href="{{ $menu['url'] }}" wire:navigate
                   @click="path = norm('{{ $menu['url'] }}'); updateIndicators();"
                   class="relative flex min-w-0 items-center justify-center rounded-full h-10 text-base min-[360px]:text-lg transition-[flex-grow,color] duration-200 ease-out z-10"
                   :class="is('{{ $menu['url'] }}') ? 'flex-[2.4] text-primary font-bold' : 'flex-1 text-text-muted hover:text-text'">
                    <span class="inline-flex items-center justify-center gap-1.5">
                        <i class="fa-solid {{ $menu['icon'] }} shrink-0"></i>
                        <span class="overflow-hidden whitespace-nowrap text-[11px] min-[360px]:text-xs font-semibold transition-[max-width,opacity] duration-200 ease-out"
                              :class="is('{{ $menu['url'] }}') ? 'max-w-20 opacity-100' : 'max-w-0 opacity-0'">
                            {{ $menu['label'] }}
                        </span>
                    </span>
                    @if (($badges[$menu['route']] ?? 0) > 0)
                        <span class="absolute top-0 right-1 {{ $menu['route'] === 'tasks' ? 'bg-danger' : 'bg-primary' }} text-white text-[9px] rounded-full min-w-4 h-4 px-1 flex items-center justify-center font-bold z-20">
                            {{ $badges[$menu['route']] > 99 ? '99+' : $badges[$menu['route']] }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>
    </nav>
</div>
