<?php

use App\Models\Task;
use Livewire\Component;

new class extends Component
{
    public function with(): array
    {
        $overdueCount = Task::all()->filter(function ($task) {
            return in_array($task->priority, ['overdue', 'critical']) && $task->progress < 100;
        })->count();

        return [
            'currentRoute' => request()->route()?->getName(),
            'taskBadgeCount' => $overdueCount,
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
        norm(p) { return p.replace(/\/+$/, '') || '/' },
        is(url) { return this.norm(new URL(url, location.origin).pathname) === this.path },
        init() {
            this.path = this.norm(location.pathname);
            this.scrolled = window.scrollY > 8;
        },
    }"
    x-on:scroll.window.passive="scrolled = window.scrollY > 8"
    x-on:livewire:navigated.window="
        const p = norm(location.pathname);
        if (p !== path) { path = p; $wire.$refresh(); }
        scrolled = window.scrollY > 8;
    "
>
    {{-- Desktop & Tablet Bottom Nav --}}
    <nav class="hidden sm:flex fixed bottom-3 lg:bottom-4 inset-x-0 z-50 justify-center px-4">
        <div class="grid grid-cols-[1fr_auto_1fr] items-center w-full max-w-7xl bg-surface border border-border rounded-full px-4 py-2 lg:px-6 lg:py-3 shadow-lg">

            {{-- Kiri: logo + teks --}}
            <div class="flex items-center gap-2 font-bold text-sm lg:text-base text-text">
                <img src="{{ asset('logo.png') }}" alt="Nestly" class="h-6 lg:h-8 w-auto">
                Nestly
            </div>

            {{-- Tengah: menu berlabel --}}
            <div class="flex items-center gap-1 lg:gap-2">
                @foreach ($menus as $menu)
                    <a href="{{ $menu['url'] }}" wire:navigate
                       class="relative flex items-center rounded-full border-2 px-3 py-1.5 lg:px-4 lg:py-2 text-xs lg:text-sm font-medium transition-colors duration-300"
                       :class="is('{{ $menu['url'] }}') ? 'border-primary text-primary' : 'border-transparent text-text-muted hover:text-text'">
                        <span class="inline-flex overflow-hidden transition-all duration-300 ease-out"
                              :class="is('{{ $menu['url'] }}') ? 'max-w-5 mr-2 opacity-100' : 'max-w-0 mr-0 opacity-0'">
                            <i class="fa-solid {{ $menu['icon'] }} transition-transform duration-300 ease-out"
                               :class="is('{{ $menu['url'] }}') ? 'translate-x-0' : 'translate-x-full'"></i>
                        </span>
                        {{ $menu['label'] }}
                        @if ($menu['route'] === 'tasks' && $taskBadgeCount > 0)
                            <span class="absolute -top-1 -right-1 bg-danger text-white text-[10px] rounded-full w-4 h-4 flex items-center justify-center">
                                {{ $taskBadgeCount }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>

            {{-- Kanan: akun + toggle tema --}}
            <div class="flex items-center justify-end gap-2 lg:gap-3">
                <a href="#" aria-label="Akun"
                   class="flex h-8 w-8 lg:h-10 lg:w-10 items-center justify-center rounded-full border-2 border-border text-text-muted hover:text-primary hover:border-primary transition-colors">
                    <i class="fa-solid fa-user text-xs lg:text-sm"></i>
                </a>
                <div class="scale-90 lg:scale-100 origin-center">
                    <x-theme-toggle />
                </div>
            </div>
        </div>
    </nav>

    {{-- Mobile Top Bar --}}
    <div class="sm:hidden fixed top-0 inset-x-0 z-50 flex items-center justify-between rounded-b-2xl px-4 py-3 transition-all duration-300"
         :class="scrolled ? 'bg-surface/90 backdrop-blur-sm shadow-md' : 'bg-transparent'">
        <div class="flex items-center gap-2 font-bold text-text">
            <img src="{{ asset('logo.png') }}" alt="Nestly" class="h-7 w-auto">
            Nestly
        </div>
        <x-theme-toggle />
    </div>

    {{-- Mobile Bottom Nav (icon-only, label muncul saat aktif) --}}
    <nav class="sm:hidden fixed bottom-0 inset-x-0 z-50 bg-surface border-t-2 border-border px-2 py-2 flex justify-around items-center">
        @foreach ($menus as $menu)
            <a href="{{ $menu['url'] }}" wire:navigate
               class="relative flex items-center rounded-full px-2.5 py-2 text-lg transition-colors duration-300"
               :class="is('{{ $menu['url'] }}') ? 'bg-tertiary text-primary' : 'text-text-muted'">
                <i class="fa-solid {{ $menu['icon'] }}"></i>
                <span class="overflow-hidden whitespace-nowrap text-xs font-medium transition-all duration-300"
                      :class="is('{{ $menu['url'] }}') ? 'max-w-24 ml-1.5 opacity-100' : 'max-w-0 ml-0 opacity-0'">
                    {{ $menu['label'] }}
                </span>
                @if ($menu['route'] === 'tasks' && $taskBadgeCount > 0)
                    <span class="absolute -top-1 -right-1 bg-danger text-white text-[9px] rounded-full w-4 h-4 flex items-center justify-center">
                        {{ $taskBadgeCount }}
                    </span>
                @endif
            </a>
        @endforeach
        <a href="#" aria-label="Akun" class="flex items-center rounded-full px-2.5 py-2 text-lg text-text-muted">
            <i class="fa-solid fa-user"></i>
        </a>
    </nav>
</div>