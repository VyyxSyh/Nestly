<?php

use App\Models\Task;
use App\Models\Subject;
use Livewire\Component;

new class extends Component
{
    public bool $showModal = false;

    public $subject_id = '';
    public $title = '';
    public $description = '';
    public $deadline = '';

    public function openModal()
    {
        $this->reset(['subject_id', 'title', 'description', 'deadline']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'nullable|exists:subjects,id',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
        ]);

        Task::create($validated);

        $this->closeModal();
    }

    public function with(): array
    {
        return [
            'tasks' => Task::with('subject')->latest()->get(),
            'subjects' => Subject::all(),
        ];
    }
};
?>

<div>
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold">Daftar Tugas</h1>
        <button wire:click="openModal" class="bg-teal-600 text-white px-4 py-2 rounded-md">
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
                <div class="font-medium">{{ $task->title }}</div>
                <div class="text-sm text-gray-500">
                    {{ $task->subject?->name ?? 'Tanpa mata kuliah' }} — Deadline: {{ $task->deadline->format('d M Y, H:i') }}
                </div>
                <div class="text-sm">
                    Status: {{ $task->status }} | Priority: {{ $task->priority }} | Progress: {{ $task->progress }}%
                </div>
            </div>
        @empty
            <p class="text-gray-500">Belum ada tugas.</p>
        @endforelse
    </div>

    @if ($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-md p-6 w-full max-w-md">
                <h2 class="text-lg font-semibold mb-4">Tambah Tugas</h2>

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

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-md border">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-md bg-teal-600 text-white">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>