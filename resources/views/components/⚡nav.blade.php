<?php

use App\Models\Schedule;
use App\Models\Task;
use Livewire\Component;

new class extends Component
{
    public function with(): array
    {
        $hariIni = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][now()->dayOfWeekIso - 1];

        return [
            'badges' => [
                'tasks' => Task::where('progress', '<', 100)->count(),
                'schedules' => Schedule::where('day', $hariIni)->count(),
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
    x-on:scroll.window.passive="scrolled = window.scrollY > 8"
    x-on:livewire:navigated.window="
        scrolled = window.scrollY > 8;
        updateIndicators();
    "
>
    {{-- Desktop & Tablet Bottom Nav --}}
    <nav class="hidden sm:flex fixed bottom-3 lg:bottom-4 inset-x-0 z-50 justify-center px-4">
        <div class="grid grid-cols-[1fr_auto_1fr] items-center w-full max-w-7xl bg-surface/30 backdrop-blur-sm border border-border/70 rounded-full px-5 py-2.5 lg:px-6 lg:py-3 shadow-lg">

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

            {{-- Kanan: toggle tema --}}
            <div class="flex items-center justify-end">
                <x-theme-toggle />
            </div>
        </div>
    </nav>

    {{-- Mobile Top Bar --}}
    <div class="sm:hidden fixed top-0 inset-x-0 z-50 flex items-center justify-between rounded-b-2xl px-4 py-3 bg-surface/30 backdrop-blur-sm border-b border-border/70 shadow-md transition-all duration-300">
        <div class="flex items-center gap-2 font-bold text-text">
            <img src="{{ asset('logo.png') }}" alt="Nestly" class="h-7 w-auto">
            Nestly
        </div>
        <x-theme-toggle />
    </div>

    {{-- Mobile Bottom Nav (pill melayang, lebar dibagi otomatis, label muncul di menu aktif) --}}
    <nav class="sm:hidden fixed bottom-3 inset-x-2 min-[360px]:inset-x-3 z-50 bg-surface/30 backdrop-blur-sm border border-border/70 rounded-full shadow-lg px-1.5 min-[360px]:px-2 py-1.5">
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
