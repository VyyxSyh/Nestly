<?php

use App\Models\Task;
use App\Models\Subject;
use App\Models\TaskChecklistItem;
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
    public $progress_mode = 'manual';
    public $newChecklistItemTitle = '';

    public ?int $confirmingDeleteId = null;

    public function openCreateModal()
    {
        $this->reset(['subject_id', 'title', 'description', 'deadline', 'editingTaskId']);
        $this->progress_mode = 'manual';
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
        $this->deadline = $task->deadline->format('Y-m-d\TH:i');
        $this->progress_mode = $task->progress_mode;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->isEditing = false;
        $this->editingTaskId = null;
    }

    public function save()
    {
        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'nullable|exists:subjects,id',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            'progress_mode' => 'required|in:manual,checklist',
        ]);

        $validated['subject_id'] = $validated['subject_id'] ?: null;

        if ($this->isEditing && $this->editingTaskId) {
            Task::findOrFail($this->editingTaskId)->update($validated);
        } else {
            Task::create($validated);
        }

        $this->closeModal();
    }

    public function incrementProgress(int $taskId)
    {
        $task = Task::findOrFail($taskId);
        $task->update(['progress' => min(100, $task->progress + 5)]);
    }

    public function decrementProgress(int $taskId)
    {
        $task = Task::findOrFail($taskId);
        $task->update(['progress' => max(0, $task->progress - 5)]);
    }

    public function toggleChecklistItem(int $itemId)
    {
        $item = TaskChecklistItem::findOrFail($itemId);
        $item->update(['is_done' => ! $item->is_done]);
        $this->recalculateProgress($item->task_id);
    }

    public function addChecklistItem(int $taskId)
    {
        $this->validate([
            'newChecklistItemTitle' => 'required|string|max:255',
        ], [], ['newChecklistItemTitle' => 'judul checklist']);

        TaskChecklistItem::create([
            'task_id' => $taskId,
            'title' => $this->newChecklistItemTitle,
            'is_done' => false,
        ]);

        $this->reset('newChecklistItemTitle');
        $this->recalculateProgress($taskId);
    }

    public function deleteChecklistItem(int $itemId)
    {
        $item = TaskChecklistItem::findOrFail($itemId);
        $taskId = $item->task_id;
        $item->delete();
        $this->recalculateProgress($taskId);
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
        $this->confirmingDeleteId = null;
    }

    public function with(): array
    {
        return [
            'tasks' => Task::with(['subject', 'checklistItems'])->latest()->get(),
            'subjects' => Subject::all(),
        ];
    }
};
?>

<div>
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold">Daftar Tugas</h1>
        <button wire:click="openCreateModal" class="bg-teal-600 text-white px-4 py-2 rounded-md">
            + Tambah Tugas
        </button>
    </div>

    <div class="space-y-3">
        @forelse ($tasks as $task)
            <div class="border rounded-md p-4 border-l-4"
                style="border-left-color:
                    @if($task->urgency_color === 'red') #E85D68
                    @elseif($task->urgency_color === 'orange') #F07845
                    @elseif($task->urgency_color === 'yellow') #E7C23B
                    @elseif($task->urgency_color === 'gray') #9AA9A4
                    @else #4CAF72
                    @endif">

                <div class="flex justify-between items-start">
                    <div>
                        <div class="font-medium">{{ $task->title }}</div>
                        <div class="text-sm text-gray-500">
                            {{ $task->subject?->name ?? 'Tanpa mata kuliah' }} — Deadline: {{ $task->deadline->format('d M Y, H:i') }}
                        </div>
                    </div>
                    <div class="flex gap-3 text-lg">
                        <button wire:click="openEditModal({{ $task->id }})" class="text-blue-600" title="Edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button wire:click="confirmDelete({{ $task->id }})" class="text-red-600" title="Hapus">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>

                <div class="text-sm mt-2 flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        @if($task->status === 'completed') bg-green-100 text-green-700
                        @elseif($task->status === 'in_progress') bg-yellow-100 text-yellow-700
                        @else bg-gray-100 text-gray-600
                        @endif">
                        {{ str($task->status)->replace('_', ' ')->title() }}
                    </span>
                    <span class="text-gray-500">Priority: {{ $task->priority }}</span>
                </div>

                <div class="mt-3">
                    @if ($task->progress_mode === 'manual')
                        <div class="flex items-center gap-3">
                            <button wire:click="decrementProgress({{ $task->id }})"
                                    class="w-7 h-7 flex items-center justify-center rounded-full border text-gray-600 hover:bg-gray-100">
                                −
                            </button>

                            <div class="flex gap-0.5 flex-1">
                                @for ($i = 0; $i < 10; $i++)
                                    @php
                                        $segmentStart = $i * 10;
                                        $filled = $task->progress >= $segmentStart + 10;
                                        $half = ! $filled && $task->progress > $segmentStart;
                                    @endphp
                                    <div class="relative h-3 flex-1 rounded-sm bg-gray-200 overflow-hidden">
                                        @if ($filled)
                                            <div class="absolute inset-0 bg-teal-500"></div>
                                        @elseif ($half)
                                            <div class="absolute inset-0 bg-teal-500" style="clip-path: polygon(0 100%, 100% 0, 100% 100%);"></div>
                                        @endif
                                    </div>
                                @endfor
                            </div>

                            <button wire:click="incrementProgress({{ $task->id }})"
                                    class="w-7 h-7 flex items-center justify-center rounded-full border text-gray-600 hover:bg-gray-100">
                                +
                            </button>

                            <span class="text-sm w-10 text-right">{{ $task->progress }}%</span>
                        </div>
                    @else
                        <div class="space-y-1">
                            @forelse ($task->checklistItems as $item)
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" wire:click="toggleChecklistItem({{ $item->id }})" @checked($item->is_done)>
                                    <span class="{{ $item->is_done ? 'line-through text-gray-400' : '' }}">{{ $item->title }}</span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-400">Belum ada checklist item. Klik Edit untuk menambahkan.</p>
                            @endforelse
                            <div class="text-sm text-gray-500 mt-1">Progress: {{ $task->progress }}%</div>
                        </div>
                    @endif
                </div>

                @if ($confirmingDeleteId === $task->id)
                    <div class="mt-3 bg-red-50 border border-red-200 rounded-md p-3 flex justify-between items-center">
                        <span class="text-sm text-red-700">Yakin mau hapus tugas ini?</span>
                        <div class="flex gap-2">
                            <button wire:click="cancelDelete" class="text-sm px-3 py-1 rounded-md border">Batal</button>
                            <button wire:click="delete({{ $task->id }})" class="text-sm px-3 py-1 rounded-md bg-red-600 text-white">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <p class="text-gray-500">Belum ada tugas.</p>
        @endforelse
    </div>

    @if ($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-md p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
                <h2 class="text-lg font-semibold mb-4">{{ $isEditing ? 'Edit Tugas' : 'Tambah Tugas' }}</h2>

                <form wire:submit="save" class="space-y-3">
                    <div>
                        <label class="block text-sm mb-1">Judul</label>
                        <input type="text" wire:model="title" class="w-full border rounded-md px-3 py-2">
                        @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Mata Kuliah</label>
                        <select wire:model="subject_id" class="w-full border rounded-md px-3 py-2">
                            <option value="">-- Tanpa Mata Kuliah --</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Deskripsi</label>
                        <textarea wire:model="description" class="w-full border rounded-md px-3 py-2"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Deadline</label>
                        <input type="datetime-local" wire:model="deadline" class="w-full border rounded-md px-3 py-2">
                        @error('deadline') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Mode Progress</label>
                        <select wire:model="progress_mode" class="w-full border rounded-md px-3 py-2">
                            <option value="manual">Manual (+/- 5%)</option>
                            <option value="checklist">Checklist (otomatis dari sub-tugas)</option>
                        </select>
                    </div>

                    @if ($isEditing && $progress_mode === 'checklist')
                        <div class="border-t pt-3 mt-1">
                            <label class="block text-sm mb-2 font-medium">Checklist Item</label>

                            <div class="space-y-1 mb-2">
                                @foreach (\App\Models\TaskChecklistItem::where('task_id', $editingTaskId)->get() as $item)
                                    <div class="flex items-center justify-between text-sm">
                                        <span>{{ $item->title }}</span>
                                        <button type="button" wire:click="deleteChecklistItem({{ $item->id }})" class="text-red-500 text-xs">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex gap-2">
                                <input type="text" wire:model="newChecklistItemTitle" placeholder="Tambah item checklist..."
                                    class="flex-1 border rounded-md px-2 py-1 text-sm">
                                <button type="button" wire:click="addChecklistItem({{ $editingTaskId }})"
                                        class="px-3 py-1 bg-teal-600 text-white rounded-md text-sm">
                                    Tambah
                                </button>
                            </div>
                            @error('newChecklistItemTitle') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-md border">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-md bg-teal-600 text-white">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>