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
    <nav class="hidden sm:flex fixed bottom-4 inset-x-0 z-50 justify-center">
        <div class="flex items-center gap-10 bg-surface border border-border rounded-full px-6 py-3 shadow-lg">
            <div class="flex items-center gap-2 font-bold text-text">
                <i class="fa-solid fa-graduation-cap text-primary"></i> Nestly
            </div>

            <div class="flex items-center gap-2">
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
                   class="relative flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium transition-colors
                          {{ $isActive ? 'bg-tertiary text-primary' : 'text-text-muted hover:text-text' }}">
                    @if ($isActive)
                        <i class="fa-solid {{ $menu['icon'] }}"></i>
                    @endif
                    {{ $menu['label'] }}
                    @if ($menu['route'] === 'tasks' && $taskBadgeCount > 0)
                        <span class="absolute -top-1 -right-1 bg-danger text-white text-[10px] rounded-full w-4 h-4 flex items-center justify-center">
                            {{ $taskBadgeCount }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>

        <div class="flex items-center gap-3">
            <label class="switch">
                <input type="checkbox" wire:click="toggleDarkMode" {{ session('dark_mode', true) ? 'checked' : '' }} />
                <div class="slider">
                    <div class="sun-moon">
                        <svg id="moon-dot-1" class="moon-dot" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
                        <svg id="moon-dot-2" class="moon-dot" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
                        <svg id="moon-dot-3" class="moon-dot" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
                        <svg id="light-ray-1" class="light-ray" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
                        <svg id="light-ray-2" class="light-ray" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
                        <svg id="light-ray-3" class="light-ray" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
                        <svg id="cloud-1" class="cloud-dark" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
                        <svg id="cloud-2" class="cloud-dark" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
                        <svg id="cloud-3" class="cloud-dark" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
                        <svg id="cloud-4" class="cloud-light" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
                        <svg id="cloud-5" class="cloud-light" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
                        <svg id="cloud-6" class="cloud-light" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
                    </div>
                    <div class="stars">
                        <svg id="star-1" class="star" viewBox="0 0 20 20"><path d="M 0 10 C 10 10,10 10 ,0 10 C 10 10 , 10 10 , 10 20 C 10 10 , 10 10 , 20 10 C 10 10 , 10 10 , 10 0 C 10 10,10 10 ,0 10 Z"></path></svg>
                        <svg id="star-2" class="star" viewBox="0 0 20 20"><path d="M 0 10 C 10 10,10 10 ,0 10 C 10 10 , 10 10 , 10 20 C 10 10 , 10 10 , 20 10 C 10 10 , 10 10 , 10 0 C 10 10,10 10 ,0 10 Z"></path></svg>
                        <svg id="star-3" class="star" viewBox="0 0 20 20"><path d="M 0 10 C 10 10,10 10 ,0 10 C 10 10 , 10 10 , 10 20 C 10 10 , 10 10 , 20 10 C 10 10 , 10 10 , 10 0 C 10 10,10 10 ,0 10 Z"></path></svg>
                        <svg id="star-4" class="star" viewBox="0 0 20 20"><path d="M 0 10 C 10 10,10 10 ,0 10 C 10 10 , 10 10 , 10 20 C 10 10 , 10 10 , 20 10 C 10 10 , 10 10 , 10 0 C 10 10,10 10 ,0 10 Z"></path></svg>
                    </div>
                </div>
            </label>
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