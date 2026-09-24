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

<div>
    {{-- Desktop & Tablet Bottom Nav --}}
    <nav class="hidden sm:flex fixed bottom-4 inset-x-0 z-50 justify-center px-4">
        <div class="flex items-center justify-between w-full max-w-7xl bg-surface border border-border rounded-full px-6 py-3 shadow-lg">
            <div class="flex items-center gap-2 font-bold text-text cursor-pointer">
                <img src="{{ asset('logo.png') }}" alt="Nestly" class="h-8 w-auto">
                Nestly
            </div>

            <div class="flex items-center gap-2 mx-auto">
                @foreach ($menus as $menu)
                    @php $isActive = $currentRoute === $menu['route']; @endphp
                    <a href="{{ $menu['url'] }}"
                    class="relative flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium transition-colors
                            {{ $isActive ? 'bg-primary text-white' : 'text-text-muted hover:text-text' }}">
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

            <x-theme-toggle />
        </div>
    </nav>

    {{-- Mobile Top Bar --}}
    <div class="sm:hidden fixed top-0 inset-x-0 z-50 bg-surface/80 backdrop-blur-sm rounded-b-2xl px-4 py-3 flex justify-between items-center">
        <div class="flex items-center gap-2 font-bold text-text">
            <i class="fa-solid fa-graduation-cap text-primary"></i> Nestly
        </div>
        <x-theme-toggle />
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