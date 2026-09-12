<?php

use App\Models\Subject;
use Livewire\Component;

new class extends Component
{
    public bool $showModal = false;
    public $name = '';
    public ?int $confirmingDeleteId = null;

    public function openModal()
    {
        $this->reset('name');
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
        ]);

        Subject::create($validated);
        $this->closeModal();
    }

    public function confirmDelete(int $id)
    {
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingDeleteId = null;
    }

    public function delete(int $id)
    {
        Subject::findOrFail($id)->delete();
        $this->confirmingDeleteId = null;
    }

    public function with(): array
    {
        return [
            'subjects' => Subject::withCount(['tasks', 'schedules'])->get(),
        ];
    }
};
?>

<div>
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold">Mata Kuliah</h1>
        <button wire:click="openModal" class="bg-teal-600 text-white px-4 py-2 rounded-md">
            + Tambah Mata Kuliah
        </button>
    </div>

    <div class="space-y-2">
        @forelse ($subjects as $subject)
            <div class="border rounded-md p-3 flex justify-between items-center">
                <div>
                    <span class="font-medium">{{ $subject->name }}</span>
                    <span class="text-sm text-gray-500 ml-2">
                        ({{ $subject->tasks_count }} tugas, {{ $subject->schedules_count }} jadwal)
                    </span>
                </div>
                <button wire:click="confirmDelete({{ $subject->id }})" class="text-red-600">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>

            @if ($confirmingDeleteId === $subject->id)
                <div class="bg-red-50 border border-red-200 rounded-md p-3 flex justify-between items-center -mt-1">
                    <span class="text-sm text-red-700">
                        Yakin hapus? Semua tugas/jadwal terkait akan kehilangan referensi mata kuliah ini.
                    </span>
                    <div class="flex gap-2">
                        <button wire:click="cancelDelete" class="text-sm px-3 py-1 rounded-md border">Batal</button>
                        <button wire:click="delete({{ $subject->id }})" class="text-sm px-3 py-1 rounded-md bg-red-600 text-white">Hapus</button>
                    </div>
                </div>
            @endif
        @empty
            <p class="text-gray-500">Belum ada mata kuliah.</p>
        @endforelse
    </div>

    @if ($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-md p-6 w-full max-w-sm">
                <h2 class="text-lg font-semibold mb-4">Tambah Mata Kuliah</h2>
                <form wire:submit="save" class="space-y-3">
                    <div>
                        <label class="block text-sm mb-1">Nama Mata Kuliah</label>
                        <input type="text" wire:model="name" class="w-full border rounded-md px-3 py-2">
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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