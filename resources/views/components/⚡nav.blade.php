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
        <div class="grid grid-cols-[1fr_auto_1fr] items-center w-full max-w-7xl bg-surface border border-border rounded-full px-5 py-2.5 lg:px-6 lg:py-3 shadow-lg">

            {{-- Kiri: logo + teks --}}
            <div class="flex items-center gap-2 font-bold text-sm md:text-base text-text">
                <img src="{{ asset('logo.png') }}" alt="Nestly" class="h-7 w-auto">
                Nestly
            </div>

            {{-- Tengah: menu berlabel --}}
            <div class="flex items-center gap-0.5 md:gap-1 lg:gap-2">
                @foreach ($menus as $menu)
                    <a href="{{ $menu['url'] }}" wire:navigate
                       class="relative flex items-center rounded-full border-2 px-3 py-2 md:px-4 text-sm font-medium transition-colors duration-300"
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

            {{-- Kanan: toggle tema --}}
            <div class="flex items-center justify-end">
                <x-theme-toggle />
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

    {{-- Mobile Bottom Nav (pill melayang, lebar dibagi otomatis, label muncul di menu aktif) --}}
    <nav class="sm:hidden fixed bottom-3 inset-x-2 min-[360px]:inset-x-3 z-50 bg-surface border border-border rounded-full shadow-lg px-1.5 min-[360px]:px-2 py-1.5 flex items-center">
        @foreach ($menus as $menu)
            <a href="{{ $menu['url'] }}" wire:navigate
               class="relative flex min-w-0 items-center justify-center rounded-full h-10 text-base min-[360px]:text-lg transition-all duration-300 ease-out"
               :class="is('{{ $menu['url'] }}') ? 'flex-[2.4] bg-tertiary text-primary' : 'flex-1 text-text-muted'">
                <i class="fa-solid {{ $menu['icon'] }} shrink-0"></i>
                <span class="overflow-hidden whitespace-nowrap text-[11px] min-[360px]:text-xs font-semibold transition-all duration-300"
                      :class="is('{{ $menu['url'] }}') ? 'max-w-20 ml-1.5 opacity-100' : 'max-w-0 ml-0 opacity-0'">
                    {{ $menu['label'] }}
                </span>
                @if ($menu['route'] === 'tasks' && $taskBadgeCount > 0)
                    <span class="absolute top-0 right-1 bg-danger text-white text-[9px] rounded-full w-4 h-4 flex items-center justify-center">
                        {{ $taskBadgeCount }}
                    </span>
                @endif
            </a>
        @endforeach
        <a href="#" aria-label="Akun"
           class="flex flex-1 min-w-0 items-center justify-center rounded-full h-10 text-base min-[360px]:text-lg text-text-muted">
            <i class="fa-solid fa-user"></i>
        </a>
    </nav>
</div>