<?php

use App\Models\Task;
use Livewire\Component;

new class extends Component
{
    public function toggleDarkMode()
    {
        session(['dark_mode' => ! session('dark_mode', true)]);
        $this->redirect(request()->fullUrl());
    }

    public function with(): array
    {
        $overdueCount = Task::all()->filter(function ($task) {
            return in_array($task->priority, ['overdue', 'critical']) && $task->progress < 100;
        })->count();

        return [
            'currentRoute' => request()->route()?->getName(),
            'taskBadgeCount' => $overdueCount,
        ];
    }
};
?>

<div>
    {{-- Desktop & Tablet Bottom Nav --}}
    <nav class="hidden sm:flex fixed bottom-0 inset-x-0 z-50 bg-surface border-t-2 border-border px-6 py-3 items-center justify-between">
        <div class="flex items-center gap-2 font-bold text-lg text-text">
            <i class="fa-solid fa-graduation-cap text-primary"></i> Nestly
        </div>

        <div class="flex items-center gap-8">
            @php
                $menus = [
                    ['route' => 'dashboard', 'label' => 'Home', 'icon' => 'fa-house', 'url' => '/'],
                    ['route' => 'tasks', 'label' => 'Task', 'icon' => 'fa-list-check', 'url' => '/tasks'],
                    ['route' => 'schedules', 'label' => 'Schedule', 'icon' => 'fa-calendar-days', 'url' => '/schedules'],
                    ['route' => 'subjects', 'label' => 'Subjects', 'icon' => 'fa-book', 'url' => '/subjects'],
                    ['route' => 'finance', 'label' => 'Finance', 'icon' => 'fa-wallet', 'url' => '/finance'],
                ];
            @endphp

            @foreach ($menus as $menu)
                @php $isActive = $currentRoute === $menu['route']; @endphp
                <a href="{{ $menu['url'] }}"
                   class="relative flex items-center gap-2 pb-1 border-b-2 text-sm font-medium
                          {{ $isActive ? 'border-primary text-primary' : 'border-transparent text-text-muted hover:text-text' }}">
                    @if ($isActive)
                        <i class="fa-solid {{ $menu['icon'] }}"></i>
                    @endif
                    {{ $menu['label'] }}
                    @if ($menu['route'] === 'tasks' && $taskBadgeCount > 0)
                        <span class="absolute -top-2 -right-3 bg-danger text-white text-[10px] rounded-full w-4 h-4 flex items-center justify-center">
                            {{ $taskBadgeCount }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>

        <div class="flex items-center gap-3">
            <button wire:click="toggleDarkMode" class="w-9 h-9 rounded-full border-2 border-border flex items-center justify-center text-text cursor-pointer">
                <i class="fa-solid {{ session('dark_mode', true) ? 'fa-sun' : 'fa-moon' }}"></i>
            </button>
            <div class="w-9 h-9 rounded-full bg-tertiary flex items-center justify-center text-primary">
                <i class="fa-solid fa-user"></i>
            </div>
        </div>
    </nav>

    {{-- Mobile Top Bar --}}
    <div class="sm:hidden fixed top-0 inset-x-0 z-50 bg-surface/80 backdrop-blur-sm rounded-b-2xl px-4 py-3 flex justify-between items-center">
        <div class="flex items-center gap-2 font-bold text-text">
            <i class="fa-solid fa-graduation-cap text-primary"></i> Nestly
        </div>
        <button wire:click="toggleDarkMode" class="w-8 h-8 rounded-full border-2 border-border flex items-center justify-center text-text cursor-pointer">
            <i class="fa-solid {{ session('dark_mode', true) ? 'fa-sun' : 'fa-moon' }} text-sm"></i>
        </button>
    </div>

    {{-- Mobile Bottom Nav (icon-only) --}}
    <nav class="sm:hidden fixed bottom-0 inset-x-0 z-50 bg-surface border-t-2 border-border px-4 py-2 flex justify-between items-center">
        @foreach ($menus as $menu)
            @php $isActive = $currentRoute === $menu['route']; @endphp
            <a href="{{ $menu['url'] }}"
               class="relative flex items-center gap-1 px-3 py-2 rounded-full text-lg
                      {{ $isActive ? 'bg-tertiary text-primary' : 'text-text-muted' }}">
                <i class="fa-solid {{ $menu['icon'] }}"></i>
                @if ($isActive)
                    <span class="text-xs font-medium">{{ $menu['label'] }}</span>
                @endif
                @if ($menu['route'] === 'tasks' && $taskBadgeCount > 0)
                    <span class="absolute -top-1 -right-1 bg-danger text-white text-[9px] rounded-full w-4 h-4 flex items-center justify-center">
                        {{ $taskBadgeCount }}
                    </span>
                @endif
            </a>
        @endforeach
        <a href="#" class="flex items-center px-3 py-2 rounded-full text-lg text-text-muted">
            <i class="fa-solid fa-user"></i>
        </a>
    </nav>
</div>