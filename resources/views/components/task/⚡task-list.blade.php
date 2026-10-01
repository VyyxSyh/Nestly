<?php

use App\Models\Subject;
use App\Models\Task;
use App\Models\TaskChecklistItem;
use Carbon\Carbon;
use Livewire\Component;

new class extends Component
{
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingTaskId = null;

    public $subject_id = '';
    public $title = '';
    public $description = '';
    public $deadline = '';
    public $deadline_time = '';
    public $progress_mode = 'manual';
    public $newChecklistItemTitle = '';
    public $search = '';
    public $filterStatus = '';
    public $filterSubject = '';
    public $sortBy = 'deadline_asc';

    public array $pendingNewItems = [];
    public array $pendingDeleteIds = [];
    public array $pendingEdits = [];
    public ?int $editingItemId = null;
    public $editingItemTitle = '';

    public ?int $confirmingDeleteId = null;

    public function openCreateModal()
    {
        $this->reset(['subject_id', 'title', 'description', 'deadline', 'deadline_time', 'editingTaskId', 'pendingNewItems', 'pendingDeleteIds', 'pendingEdits']);
        $this->progress_mode = 'manual';
        $this->pendingNewItems = [''];
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEditModal(int $taskId)
    {
        $task = Task::findOrFail($taskId);

        $this->editingTaskId = $task->id;
        $this->subject_id = $task->subject_id ?? '';
        $this->title = $task->title;
        $this->description = $task->description;
        $this->deadline = $task->deadline->format('Y-m-d');
        $this->deadline_time = $task->deadline_time ? Carbon::parse($task->deadline_time)->format('H:i') : '';
        $this->progress_mode = $task->progress_mode;
        $this->pendingNewItems = [''];
        $this->pendingDeleteIds = [];
        $this->pendingEdits = [];
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->isEditing = false;
        $this->editingTaskId = null;
        $this->pendingNewItems = [''];
        $this->pendingDeleteIds = [];
        $this->pendingEdits = [];
        $this->editingItemId = null;
        $this->editingItemTitle = '';
    }

    public function updated($property, $value)
    {
        if ($property === 'progress_mode' && $value === 'checklist' && empty($this->pendingNewItems)) {
            $this->pendingNewItems = [''];
        }

        if (str_starts_with($property, 'pendingNewItems.')) {
            $lastIndex = array_key_last($this->pendingNewItems);
            if (trim($this->pendingNewItems[$lastIndex]) !== '') {
                $this->pendingNewItems[] = '';
            }
        }
    }

    public function unstageNewChecklistItem(int $index)
    {
        unset($this->pendingNewItems[$index]);
        $this->pendingNewItems = array_values($this->pendingNewItems);

        if (empty($this->pendingNewItems)) {
            $this->pendingNewItems = [''];
        }
    }

    public function startEditExisting(int $itemId, string $currentTitle)
    {
        $this->editingItemId = $itemId;
        $this->editingItemTitle = $currentTitle;
    }

    public function cancelEditExisting()
    {
        $this->editingItemId = null;
        $this->editingItemTitle = '';
    }

    public function confirmEditExisting()
    {
        if ($this->editingItemId) {
            $this->pendingEdits[$this->editingItemId] = $this->editingItemTitle;
        }
        $this->editingItemId = null;
        $this->editingItemTitle = '';
    }

    public function stageDeleteExistingItem(int $itemId)
    {
        if (! in_array($itemId, $this->pendingDeleteIds)) {
            $this->pendingDeleteIds[] = $itemId;
        }
    }

    public function unstageDeleteExistingItem(int $itemId)
    {
        $this->pendingDeleteIds = array_values(array_diff($this->pendingDeleteIds, [$itemId]));
    }

    public function save()
    {
        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'nullable|exists:subjects,id',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            'deadline_time' => 'nullable',
            'progress_mode' => 'required|in:manual,checklist',
        ]);

        $validated['subject_id'] = $validated['subject_id'] ?: null;
        $validated['deadline_time'] = $validated['deadline_time'] ?: null;

        if ($this->isEditing && $this->editingTaskId) {
            $task = Task::findOrFail($this->editingTaskId);
            $task->update($validated);
        } else {
            $task = Task::create($validated);
        }

        if (! empty($this->pendingDeleteIds)) {
            TaskChecklistItem::whereIn('id', $this->pendingDeleteIds)->delete();
        }

        foreach ($this->pendingEdits as $itemId => $newTitle) {
            if (trim($newTitle) !== '') {
                TaskChecklistItem::where('id', $itemId)->update(['title' => $newTitle]);
            }
        }

        foreach ($this->pendingNewItems as $itemTitle) {
            if (trim($itemTitle) !== '') {
                TaskChecklistItem::create([
                    'task_id' => $task->id,
                    'title' => $itemTitle,
                    'is_done' => false,
                ]);
            }
        }

        $this->recalculateProgress($task->id);
        $this->dispatch('badges-updated');
        $this->closeModal();
    }

    public function incrementProgress(int $taskId)
    {
        $task = Task::findOrFail($taskId);
        $task->update(['progress' => min(100, $task->progress + 5)]);
        $this->dispatch('badges-updated');
    }

    public function decrementProgress(int $taskId)
    {
        $task = Task::findOrFail($taskId);
        $task->update(['progress' => max(0, $task->progress - 5)]);
        $this->dispatch('badges-updated');
    }

    public function toggleChecklistItem(int $itemId)
    {
        $item = TaskChecklistItem::findOrFail($itemId);
        $item->update(['is_done' => ! $item->is_done]);
        $this->recalculateProgress($item->task_id);
        $this->dispatch('badges-updated');
    }

    private function recalculateProgress(int $taskId): void
    {
        $task = Task::findOrFail($taskId);
        $total = $task->checklistItems()->count();
        $done = $task->checklistItems()->where('is_done', true)->count();
        $newProgress = $total > 0 ? (int) round(($done / $total) * 100) : 0;

        $task->update(['progress' => $newProgress]);
    }

    public function confirmDelete(int $taskId)
    {
        $this->confirmingDeleteId = $taskId;
    }

    public function cancelDelete()
    {
        $this->confirmingDeleteId = null;
    }

    public function delete(int $taskId)
    {
        Task::findOrFail($taskId)->delete();
        $this->dispatch('badges-updated');
        $this->confirmingDeleteId = null;
    }

    public function with(): array
    {
        $query = Task::with(['subject', 'checklistItems']);

        if ($this->search) {
            $query->where('title', 'like', '%'.$this->search.'%');
        }

        if ($this->filterSubject) {
            $query->where('subject_id', $this->filterSubject);
        }

        $tasks = $query->get();

        if ($this->filterStatus) {
            $tasks = $tasks->filter(fn ($t) => $t->status === $this->filterStatus);
        }

        $tasks = match ($this->sortBy) {
            'deadline_asc' => $tasks->sortBy('deadline'),
            'deadline_desc' => $tasks->sortByDesc('deadline'),
            'progress_asc' => $tasks->sortBy('progress'),
            'progress_desc' => $tasks->sortByDesc('progress'),
            'newest' => $tasks->sortByDesc('created_at'),
            'subject' => $tasks->sortBy(fn ($t) => $t->subject?->name ?? 'zzz'),
            default => $tasks->sortBy('deadline'),
        };
        $tasks = $tasks->sortBy(fn ($task) => $task->status === 'completed');

        return [
            'tasks' => $tasks->values(),
            'subjects' => Subject::all(),
            'editingTaskExistingItems' => ($this->isEditing && $this->editingTaskId)
                ? TaskChecklistItem::where('task_id', $this->editingTaskId)->get()
                : collect(),
        ];
    }
};
?>

@php
    $taskCard = function ($task) {
        $accent = $task->subject?->accent_color ?? '#9CA3AF';
        $statusColor = match ($task->status) {
            'completed' => '#22C55E',
            'in_progress' => '#3B82F6',
            default => '#9CA3AF',
        };
        $urgencyHex = match ($task->urgency_color) {
            'red' => '#EF4444',
            'orange' => '#F59E0B',
            'yellow' => '#EAB308',
            'gray' => '#9CA3AF',
            default => '#22C55E',
        };
        return [$accent, $statusColor, $urgencyHex];
    };
@endphp

<div class="text-text">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold">Daftar Tugas</h1>
        <button wire:click="openCreateModal" class="bg-primary hover:bg-secondary text-white px-4 py-2 rounded-md cursor-pointer transition-colors">
            + Tambah Tugas
        </button>
    </div>

    <div class="grid grid-cols-2 md:flex md:flex-wrap gap-2 mb-4">
        <input type="text" wire:model.live="search" placeholder="Cari judul tugas..."
            class="col-span-1 border border-border bg-surface text-text placeholder:text-text-muted rounded-md px-3 py-2 text-sm min-w-0 md:flex-1 md:min-w-[150px]">

        <select wire:model.live="filterStatus" class="col-span-1 min-w-0 border border-border bg-surface text-text rounded-md px-3 py-2 text-sm">
            <option value="">Semua Status</option>
            <option value="not_started">Not Started</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
        </select>

        <select wire:model.live="filterSubject" class="col-span-1 min-w-0 border border-border bg-surface text-text rounded-md px-3 py-2 text-sm md:flex-1">
            <option value="">Semua Mata Kuliah</option>
            @foreach ($subjects as $subject)
                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
            @endforeach
        </select>

        <select wire:model.live="sortBy" class="col-span-1 min-w-0 border border-border bg-surface text-text rounded-md px-3 py-2 text-sm md:flex-1">
            <option value="deadline_asc">Deadline Terdekat</option>
            <option value="deadline_desc">Deadline Terjauh</option>
            <option value="progress_desc">Progress Tertinggi</option>
            <option value="progress_asc">Progress Terendah</option>
            <option value="newest">Terbaru Dibuat</option>
            <option value="subject">Mata Kuliah (A-Z)</option>
        </select>
    </div>

    @if ($tasks->isEmpty())
        <p class="text-text-muted mt-4">Belum ada tugas.</p>
    @else
        <div class="flex flex-col md:flex-row gap-6 mt-4">
            {{-- Kolom kiri: index genap (0, 2, 4, ...) --}}
            <div class="contents md:flex-1 md:flex md:flex-col md:gap-6">
                @foreach ($tasks as $index => $task)
                    @continue($index % 2 !== 0)
                    @php [$accent, $statusColor, $urgencyHex] = $taskCard($task); @endphp
                    <div style="--mobile-order: {{ $index }}; border-color: {{ $accent }}; box-shadow: 6px 6px 0px {{ $accent }};" class="[order:var(--mobile-order)] md:order-none relative border-2 rounded-2xl p-4 sm:p-5 pt-5 sm:pt-6 bg-surface">

                        {{-- Badges desktop di border --}}
                        <div class="hidden lg:block absolute -top-3 left-4 bg-surface px-3 py-1 rounded-full border-2 text-xs font-semibold capitalize whitespace-nowrap"
                            style="border-color: {{ $urgencyHex }}; color: {{ $urgencyHex }};">
                            {{ $task->priority }}
                        </div>

                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-surface px-3 py-1 rounded-full border-2 text-xs font-semibold whitespace-nowrap max-w-[80%] truncate"
                            style="border-color: {{ $accent }}; color: {{ $accent }};">
                            {{ $task->subject?->name ?? 'Tanpa Mata Kuliah' }}
                        </div>

                        <div class="hidden lg:block absolute -top-3 right-4 bg-surface px-3 py-1 rounded-full border-2 text-xs font-semibold whitespace-nowrap"
                            style="border-color: {{ $statusColor }}; color: {{ $statusColor }};">
                            {{ str($task->status)->replace('_', ' ')->title() }}
                        </div>

                        {{-- Badges mobile & tablet di dalam card sejajar kanan-kiri --}}
                        <div class="flex lg:hidden justify-between items-center mb-3">
                            <div class="bg-surface px-2.5 py-0.5 rounded-full border-2 text-xs font-semibold capitalize whitespace-nowrap"
                                style="border-color: {{ $urgencyHex }}; color: {{ $urgencyHex }};">
                                {{ $task->priority }}
                            </div>
                            <div class="bg-surface px-2.5 py-0.5 rounded-full border-2 text-xs font-semibold whitespace-nowrap"
                                style="border-color: {{ $statusColor }}; color: {{ $statusColor }};">
                                {{ str($task->status)->replace('_', ' ')->title() }}
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-2">
                            <div>
                                <h3 class="font-bold text-lg mb-1">{{ $task->title }}</h3>
                                @if ($task->description)
                                    <p class="text-sm text-text-muted">{{ $task->description }}</p>
                                @endif
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="font-semibold text-sm">Progress</span>
                                    <span class="text-sm">{{ $task->progress }}%</span>
                                </div>

                                <div class="w-full h-2 bg-border rounded-full overflow-hidden mb-2">
                                    <div class="h-full bg-primary" style="width: {{ $task->progress }}%"></div>
                                </div>

                                @if ($task->progress_mode === 'manual')
                                    <div class="flex justify-between w-full">
                                        <button wire:click="decrementProgress({{ $task->id }})"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg border-2 border-primary text-primary cursor-pointer active:scale-90 active:bg-tertiary transition-transform duration-100">
                                            <i class="fa-solid fa-minus text-xs"></i>
                                        </button>
                                        <button wire:click="incrementProgress({{ $task->id }})"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg border-2 border-primary text-primary cursor-pointer active:scale-90 active:bg-tertiary transition-transform duration-100">
                                            <i class="fa-solid fa-plus text-xs"></i>
                                        </button>
                                    </div>
                                @endif

                                <div class="mt-3">
                                    <div class="font-semibold text-sm">Deadline</div>
                                    <div class="text-sm text-text-muted">{{ $task->deadline_formatted }}</div>
                                </div>
                            </div>
                        </div>

                        @if ($task->progress_mode === 'checklist')
                            <div class="mt-4 pt-3 border-t border-border">
                                <div class="text-center font-semibold text-sm mb-2">Todo List</div>
                                <div class="space-y-1">
                                    @forelse ($task->checklistItems as $item)
                                        <label class="flex items-center gap-2 text-sm">
                                            <input type="checkbox" wire:click="toggleChecklistItem({{ $item->id }})" @checked($item->is_done)
                                                class="w-4 h-4 rounded border-2" style="accent-color: {{ $accent }}">
                                            <span class="{{ $item->is_done ? 'line-through text-text-muted' : '' }}">{{ $item->title }}</span>
                                        </label>
                                    @empty
                                        <p class="text-sm text-text-muted text-center">Belum ada checklist item.</p>
                                    @endforelse
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-center gap-5 mt-4">
                            <button wire:click="openEditModal({{ $task->id }})"
                                    class="w-9 h-9 flex items-center justify-center rounded-lg border-2 border-blue-500 bg-blue-500 text-white cursor-pointer
                                        hover:bg-transparent hover:scale-110 hover:shadow-[0_0_10px_rgba(59,130,246,0.7)] hover:text-blue-500
                                        active:scale-90 transition-all duration-150">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button wire:click="confirmDelete({{ $task->id }})"
                                    class="w-9 h-9 flex items-center justify-center rounded-lg border-2 border-red-500 bg-red-500 text-white cursor-pointer
                                        hover:bg-transparent hover:scale-110 hover:shadow-[0_0_10px_rgba(239,68,68,0.7)] hover:text-red-500
                                        active:scale-90 transition-all duration-150">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>

                        @if ($confirmingDeleteId === $task->id)
                            <div class="mt-3 bg-danger/10 border border-danger/40 rounded-md p-3 flex justify-between items-center">
                                <span class="text-sm text-danger">Yakin mau hapus tugas ini?</span>
                                <div class="flex gap-2">
                                    <button wire:click="cancelDelete" class="text-sm px-3 py-1 rounded-md border border-border text-text">Batal</button>
                                    <button wire:click="delete({{ $task->id }})" class="text-sm px-3 py-1 rounded-md bg-danger text-white">Hapus</button>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Kolom kanan: index ganjil (1, 3, 5, ...) --}}
            <div class="contents md:flex-1 md:flex md:flex-col md:gap-6">
                @foreach ($tasks as $index => $task)
                    @continue($index % 2 === 0)
                    @php [$accent, $statusColor, $urgencyHex] = $taskCard($task); @endphp
                    <div style="--mobile-order: {{ $index }}; border-color: {{ $accent }}; box-shadow: 6px 6px 0px {{ $accent }};" class="[order:var(--mobile-order)] md:order-none relative border-2 rounded-2xl p-4 sm:p-5 pt-5 sm:pt-6 bg-surface">

                        {{-- Badges desktop di border --}}
                        <div class="hidden lg:block absolute -top-3 left-4 bg-surface px-3 py-1 rounded-full border-2 text-xs font-semibold capitalize whitespace-nowrap"
                            style="border-color: {{ $urgencyHex }}; color: {{ $urgencyHex }};">
                            {{ $task->priority }}
                        </div>

                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-surface px-3 py-1 rounded-full border-2 text-xs font-semibold whitespace-nowrap max-w-[80%] truncate"
                            style="border-color: {{ $accent }}; color: {{ $accent }};">
                            {{ $task->subject?->name ?? 'Tanpa Mata Kuliah' }}
                        </div>

                        <div class="hidden lg:block absolute -top-3 right-4 bg-surface px-3 py-1 rounded-full border-2 text-xs font-semibold whitespace-nowrap"
                            style="border-color: {{ $statusColor }}; color: {{ $statusColor }};">
                            {{ str($task->status)->replace('_', ' ')->title() }}
                        </div>

                        {{-- Badges mobile & tablet di dalam card sejajar kanan-kiri --}}
                        <div class="flex lg:hidden justify-between items-center mb-3">
                            <div class="bg-surface px-2.5 py-0.5 rounded-full border-2 text-xs font-semibold capitalize whitespace-nowrap"
                                style="border-color: {{ $urgencyHex }}; color: {{ $urgencyHex }};">
                                {{ $task->priority }}
                            </div>
                            <div class="bg-surface px-2.5 py-0.5 rounded-full border-2 text-xs font-semibold whitespace-nowrap"
                                style="border-color: {{ $statusColor }}; color: {{ $statusColor }};">
                                {{ str($task->status)->replace('_', ' ')->title() }}
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-2">
                            <div>
                                <h3 class="font-bold text-lg mb-1">{{ $task->title }}</h3>
                                @if ($task->description)
                                    <p class="text-sm text-text-muted">{{ $task->description }}</p>
                                @endif
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="font-semibold text-sm">Progress</span>
                                    <span class="text-sm">{{ $task->progress }}%</span>
                                </div>

                                <div class="w-full h-2 bg-border rounded-full overflow-hidden mb-2">
                                    <div class="h-full bg-green-500" style="width: {{ $task->progress }}%"></div>
                                </div>

                                @if ($task->progress_mode === 'manual')
                                    <div class="flex justify-between w-full">
                                        <button wire:click="decrementProgress({{ $task->id }})"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg border-2 border-green-500 text-green-600 cursor-pointer active:scale-90 active:bg-green-500/20 transition-transform duration-100">
                                            <i class="fa-solid fa-minus text-xs"></i>
                                        </button>
                                        <button wire:click="incrementProgress({{ $task->id }})"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg border-2 border-green-500 text-green-600 cursor-pointer active:scale-90 active:bg-green-500/20 transition-transform duration-100">
                                            <i class="fa-solid fa-plus text-xs"></i>
                                        </button>
                                    </div>
                                @endif

                                <div class="mt-3">
                                    <div class="font-semibold text-sm">Deadline</div>
                                    <div class="text-sm text-text-muted">{{ $task->deadline_formatted }}</div>
                                </div>
                            </div>
                        </div>

                        @if ($task->progress_mode === 'checklist')
                            <div class="mt-4 pt-3 border-t border-border">
                                <div class="text-center font-semibold text-sm mb-2">Todo List</div>
                                <div class="space-y-1">
                                    @forelse ($task->checklistItems as $item)
                                        <label class="flex items-center gap-2 text-sm">
                                            <input type="checkbox" wire:click="toggleChecklistItem({{ $item->id }})" @checked($item->is_done)
                                                class="w-4 h-4 rounded border-2" style="accent-color: {{ $accent }}">
                                            <span class="{{ $item->is_done ? 'line-through text-text-muted' : '' }}">{{ $item->title }}</span>
                                        </label>
                                    @empty
                                        <p class="text-sm text-text-muted text-center">Belum ada checklist item.</p>
                                    @endforelse
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-center gap-5 mt-4">
                            <button wire:click="openEditModal({{ $task->id }})"
                                    class="w-9 h-9 flex items-center justify-center rounded-lg border-2 border-blue-500 bg-blue-500 text-white cursor-pointer
                                        hover:bg-transparent hover:scale-110 hover:shadow-[0_0_10px_rgba(59,130,246,0.7)] hover:text-blue-500
                                        active:scale-90 transition-all duration-150">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button wire:click="confirmDelete({{ $task->id }})"
                                    class="w-9 h-9 flex items-center justify-center rounded-lg border-2 border-red-500 bg-red-500 text-white cursor-pointer
                                        hover:bg-transparent hover:scale-110 hover:shadow-[0_0_10px_rgba(239,68,68,0.7)] hover:text-red-500
                                        active:scale-90 transition-all duration-150">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>

                        @if ($confirmingDeleteId === $task->id)
                            <div class="mt-3 bg-danger/10 border border-danger/40 rounded-md p-3 flex justify-between items-center">
                                <span class="text-sm text-danger">Yakin mau hapus tugas ini?</span>
                                <div class="flex gap-2">
                                    <button wire:click="cancelDelete" class="text-sm px-3 py-1 rounded-md border border-border text-text">Batal</button>
                                    <button wire:click="delete({{ $task->id }})" class="text-sm px-3 py-1 rounded-md bg-danger text-white">Hapus</button>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if ($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-surface text-text rounded-md p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
                <h2 class="text-lg font-semibold mb-4">{{ $isEditing ? 'Edit Tugas' : 'Tambah Tugas' }}</h2>

                <form wire:submit="save" class="space-y-3">
                    <div>
                        <label class="block text-sm mb-1">Judul</label>
                        <input type="text" wire:model="title" class="w-full border border-border bg-bg text-text rounded-md px-3 py-2">
                        @error('title') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Mata Kuliah</label>
                        <select wire:model="subject_id" class="w-full border border-border bg-bg text-text rounded-md px-3 py-2">
                            <option value="">-- Tanpa Mata Kuliah --</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Deskripsi</label>
                        <textarea wire:model="description" class="w-full border border-border bg-bg text-text rounded-md px-3 py-2"></textarea>
                    </div>

                    <div class="flex gap-2">
                        <div class="flex-1">
                            <label class="block text-sm mb-1">Deadline (tanggal)</label>
                            <input type="date" wire:model="deadline" class="w-full border border-border bg-bg text-text rounded-md px-3 py-2">
                            @error('deadline') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm mb-1">Jam (opsional)</label>
                            <input type="time" wire:model="deadline_time" class="w-full border border-border bg-bg text-text rounded-md px-3 py-2">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Mode Progress</label>
                        <select wire:model.change.live="progress_mode" class="w-full border border-border bg-bg text-text rounded-md px-3 py-2">
                            <option value="manual">Manual (+/- 5%)</option>
                            <option value="checklist">Checklist (otomatis dari sub-tugas)</option>
                        </select>
                    </div>

                    @if ($progress_mode === 'checklist')
                        <div class="border-t border-border pt-3 mt-1">
                            <label class="block text-sm mb-2 font-medium">Checklist Item</label>

                            <div class="space-y-1 mb-2">
                                {{-- item yang sudah ada di database --}}
                                @foreach ($editingTaskExistingItems as $item)
                                    @if (! in_array($item->id, $pendingDeleteIds))
                                        <div class="flex items-center gap-2 text-sm">
                                            @if ($editingItemId === $item->id)
                                                <input type="text" wire:model="editingItemTitle" wire:keydown.enter.prevent="confirmEditExisting"
                                                    class="flex-1 border border-border bg-bg text-text rounded-md px-2 py-1 text-sm">
                                                <button type="button" wire:click="confirmEditExisting" class="text-success text-xs">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                                <button type="button" wire:click="cancelEditExisting" class="text-text-muted text-xs">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            @else
                                                <span class="flex-1">{{ $pendingEdits[$item->id] ?? $item->title }}</span>
                                                <button type="button" wire:click="startEditExisting({{ $item->id }}, '{{ addslashes($pendingEdits[$item->id] ?? $item->title) }}')" class="text-blue-500 text-xs">
                                                    <i class="fa-solid fa-pen"></i>
                                                </button>
                                                <button type="button" wire:click="stageDeleteExistingItem({{ $item->id }})" class="text-danger text-xs">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach

                                {{-- item ditandai untuk dihapus (bisa dibatalkan) --}}
                                @foreach ($editingTaskExistingItems as $item)
                                    @if (in_array($item->id, $pendingDeleteIds))
                                        <div class="flex items-center justify-between text-sm text-text-muted">
                                            <span class="line-through">{{ $item->title }} (akan dihapus)</span>
                                            <button type="button" wire:click="unstageDeleteExistingItem({{ $item->id }})" class="text-blue-500 text-xs">
                                                Batal
                                            </button>
                                        </div>
                                    @endif
                                @endforeach

                                {{-- input baru, auto-tambah slot kosong --}}
                                @foreach ($pendingNewItems as $index => $itemTitle)
                                    <div class="flex items-center gap-2">
                                        <input type="text" wire:model.live="pendingNewItems.{{ $index }}"
                                            placeholder="Tambah item checklist..."
                                            class="flex-1 border border-border bg-bg text-text placeholder:text-text-muted rounded-md px-2 py-1 text-sm">
                                        @if (count($pendingNewItems) > 1)
                                            <button type="button" wire:click="unstageNewChecklistItem({{ $index }})" class="text-danger text-xs">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <p class="text-xs text-text-muted mt-1">Perubahan checklist baru tersimpan permanen setelah klik "Simpan".</p>
                        </div>
                    @endif

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-md border border-border  cursor-pointer text-text">Batal</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="save" class="px-4 py-2 rounded-md bg-primary hover:bg-secondary text-white cursor-pointer transition-colors disabled:cursor-wait disabled:opacity-60">
                            <span wire:loading.remove wire:target="save">Simpan</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
