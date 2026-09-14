<?php

use App\Models\FinanceRecord;
use App\Models\Budget;
use Livewire\Component;
use Carbon\Carbon;

new class extends Component
{
    public bool $showRecordModal = false;
    public bool $isEditingRecord = false;
    public ?int $editingRecordId = null;

    public $type = 'expense';
    public $category = '';
    public $amount = '';
    public $date = '';
    public $note = '';

    public bool $showBudgetModal = false;
    public $budget_amount = '';

    public ?int $confirmingDeleteId = null;

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
    }

    public function openRecordModal()
    {
        $this->reset(['type', 'category', 'amount', 'note', 'editingRecordId']);
        $this->type = 'expense';
        $this->date = now()->format('Y-m-d');
        $this->isEditingRecord = false;
        $this->showRecordModal = true;
    }

    public function openEditRecordModal(int $id)
    {
        $record = FinanceRecord::findOrFail($id);

        $this->editingRecordId = $record->id;
        $this->type = $record->type;
        $this->category = $record->category;
        $this->amount = $record->amount;
        $this->date = $record->date->format('Y-m-d');
        $this->note = $record->note;
        $this->isEditingRecord = true;
        $this->showRecordModal = true;
    }

    public function closeRecordModal()
    {
        $this->showRecordModal = false;
        $this->isEditingRecord = false;
        $this->editingRecordId = null;
    }

    public function saveRecord()
    {
        $validated = $this->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        if ($this->isEditingRecord && $this->editingRecordId) {
            FinanceRecord::findOrFail($this->editingRecordId)->update($validated);
        } else {
            FinanceRecord::create($validated);
        }

        $this->closeRecordModal();
    }

    public function confirmDelete(int $id)
    {
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingDeleteId = null;
    }

    public function deleteRecord(int $id)
    {
        FinanceRecord::findOrFail($id)->delete();
        $this->confirmingDeleteId = null;
    }

    public function openBudgetModal()
    {
        $effective = $this->getEffectiveBudget();
        $this->budget_amount = $effective?->amount ?? '';
        $this->showBudgetModal = true;
    }

    public function closeBudgetModal()
    {
        $this->showBudgetModal = false;
    }

    public function saveBudget()
    {
        $validated = $this->validate([
            'budget_amount' => 'required|numeric|min:0',
        ]);

        Budget::updateOrCreate(
            ['month' => now()->month, 'year' => now()->year],
            ['amount' => $validated['budget_amount']]
        );

        $this->closeBudgetModal();
    }

    public function with(): array
    {
        $records = FinanceRecord::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->orderByDesc('date')
            ->get();

        $totalIncome = $records->where('type', 'income')->sum('amount');
        $totalExpense = $records->where('type', 'expense')->sum('amount');
        $netBalance = $totalIncome - $totalExpense;

        $currentMonthBudget = Budget::where('month', now()->month)->where('year', now()->year)->first();
        $effectiveBudget = $this->getEffectiveBudget();
        $budgetAmount = $effectiveBudget?->amount ?? 0;
        $isInheritedBudget = ! $currentMonthBudget && $effectiveBudget;
        $budgetRemaining = $budgetAmount - $totalExpense;
        $percentUsed = $budgetAmount > 0 ? min(100, round(($totalExpense / $budgetAmount) * 100)) : 0;

        return [
            'records' => $records,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netBalance' => $netBalance,
            'budgetAmount' => $budgetAmount,
            'budgetRemaining' => $budgetRemaining,
            'percentUsed' => $percentUsed,
            'isOverBudget' => $budgetAmount > 0 && $totalExpense > $budgetAmount,
            'isInheritedBudget' => $isInheritedBudget,
        ];
    }

    private function getEffectiveBudget(): ?Budget
    {
        $current = Budget::where('month', now()->month)->where('year', now()->year)->first();

        if ($current) {
            return $current;
        }

        return Budget::where(function ($query) {
                $query->where('year', '<', now()->year)
                    ->orWhere(function ($q) {
                        $q->where('year', now()->year)->where('month', '<', now()->month);
                    });
            })
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->first();
    }


};
?>

<div>
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold">Finance Tracker — {{ now()->translatedFormat('F Y') }}</h1>
        <div class="flex gap-2">
            <button wire:click="openBudgetModal" class="border px-4 py-2 rounded-md">Atur Budget</button>
            <button wire:click="openRecordModal" class="bg-teal-600 text-white px-4 py-2 rounded-md">
                + Tambah Transaksi
            </button>
        </div>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-3 gap-3 mb-4">
        <div class="border rounded-md p-3">
            <div class="text-sm text-gray-500">Pemasukan</div>
            <div class="font-semibold text-green-600">Rp{{ number_format($totalIncome, 0, ',', '.') }}</div>
        </div>
        <div class="border rounded-md p-3">
            <div class="text-sm text-gray-500">Pengeluaran</div>
            <div class="font-semibold text-red-600">Rp{{ number_format($totalExpense, 0, ',', '.') }}</div>
        </div>
        <div class="border rounded-md p-3">
            <div class="text-sm text-gray-500">Sisa Saldo</div>
            <div class="font-semibold {{ $netBalance < 0 ? 'text-red-600' : 'text-gray-800' }}">
                Rp{{ number_format($netBalance, 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- Budget bar --}}
    @if ($budgetAmount > 0)
        <div class="mb-4">
            <div class="flex justify-between text-sm mb-1">
                <span>
                    Budget Belanja Bulan Ini: Rp{{ number_format($budgetAmount, 0, ',', '.') }} (sisa Rp{{ number_format($budgetRemaining, 0, ',', '.') }})
                    @if ($isInheritedBudget)
                        <span class="text-xs text-gray-400 italic">(dari bulan sebelumnya)</span>
                    @endif
                </span>
                <span class="{{ $isOverBudget ? 'text-red-600 font-medium' : 'text-gray-500' }}">
                    {{ $percentUsed }}% terpakai @if($isOverBudget) (melebihi budget!) @endif
                </span>
            </div>
            <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full {{ $isOverBudget ? 'bg-red-500' : ($percentUsed >= 80 ? 'bg-orange-400' : 'bg-teal-500') }}"
                    style="width: {{ $percentUsed }}%"></div>
            </div>
        </div>
    @else
        <p class="text-sm text-gray-400 mb-4">Belum ada budget. Klik "Atur Budget" untuk menentukan.</p>
    @endif

    {{-- Records list --}}
    <div class="space-y-2">
        @forelse ($records as $record)
            <div class="border rounded-md p-3 flex justify-between items-center">
                <div>
                    <div class="font-medium">
                        {{ $record->category }}
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $record->type === 'income' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $record->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-500">{{ $record->date->format('d M Y') }} @if($record->note) — {{ $record->note }} @endif</div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="font-medium {{ $record->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                        Rp{{ number_format($record->amount, 0, ',', '.') }}
                    </span>
                    <button wire:click="openEditRecordModal({{ $record->id }})" class="text-blue-600">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button wire:click="confirmDelete({{ $record->id }})" class="text-red-600">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>

            @if ($confirmingDeleteId === $record->id)
                <div class="bg-red-50 border border-red-200 rounded-md p-3 flex justify-between items-center -mt-1">
                    <span class="text-sm text-red-700">Yakin hapus transaksi ini?</span>
                    <div class="flex gap-2">
                        <button wire:click="cancelDelete" class="text-sm px-3 py-1 rounded-md border">Batal</button>
                        <button wire:click="deleteRecord({{ $record->id }})" class="text-sm px-3 py-1 rounded-md bg-red-600 text-white">Hapus</button>
                    </div>
                </div>
            @endif
        @empty
            <p class="text-gray-500">Belum ada transaksi bulan ini.</p>
        @endforelse
    </div>

    {{-- Modal tambah/edit transaksi --}}
    @if ($showRecordModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-md p-6 w-full max-w-sm">
                <h2 class="text-lg font-semibold mb-4">{{ $isEditingRecord ? 'Edit Transaksi' : 'Tambah Transaksi' }}</h2>
                <form wire:submit="saveRecord" class="space-y-3">
                    <div>
                        <label class="block text-sm mb-1">Tipe</label>
                        <select wire:model="type" class="w-full border rounded-md px-3 py-2">
                            <option value="expense">Pengeluaran</option>
                            <option value="income">Pemasukan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Kategori</label>
                        <input type="text" wire:model="category" class="w-full border rounded-md px-3 py-2" placeholder="misal: makan, transport, uang saku">
                        @error('category') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Nominal (Rp)</label>
                        <input type="number" wire:model="amount" class="w-full border rounded-md px-3 py-2">
                        @error('amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Tanggal</label>
                        <input type="date" wire:model="date" class="w-full border rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Catatan (opsional)</label>
                        <textarea wire:model="note" class="w-full border rounded-md px-3 py-2"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeRecordModal" class="px-4 py-2 rounded-md border">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-md bg-teal-600 text-white">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal atur budget --}}
    @if ($showBudgetModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-md p-6 w-full max-w-sm">
                <h2 class="text-lg font-semibold mb-4">Atur Budget — {{ now()->translatedFormat('F Y') }}</h2>
                <form wire:submit="saveBudget" class="space-y-3">
                    <div>
                        <label class="block text-sm mb-1">Nominal Budget (Rp)</label>
                        <input type="number" wire:model="budget_amount" class="w-full border rounded-md px-3 py-2">
                        @error('budget_amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeBudgetModal" class="px-4 py-2 rounded-md border">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-md bg-teal-600 text-white">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>