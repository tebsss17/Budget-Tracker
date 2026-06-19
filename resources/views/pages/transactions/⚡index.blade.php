<?php

use Livewire\Component;
use App\Models\Transaction;

new class extends Component
{
    // VARIABLES
    public $showAddModal = false;
    public $showEditModal = false;

    public $category_id = '';
    public $amount = '';
    public $type = 'Expense';
    public $description = '';
    public $transaction_date = '';

    public $selectedTransaction = '';


    // QUERY FUNCTIONS
    public function mount()
    {
        $this->transaction_date = now()->format('Y-m-d');
    }

    public function transactions()
    {
        return Auth::user()
            ->transaction()
            ->with('category')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function categories()
    {
        return Auth::user()->category()->orderBy('name')->get();
    }

    public function getStatsProperty()
    {
        $transactions = $this->transactions();

        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');
        $balance = $income - $expense;

        return[
            'income' => $income,
            'expense' => $expense,
            'balance' => $balance,
        ];
    }


    // MODAL FUNCTIONS
    public function addModal()
    {
        $this->type = 'Expense';
        $this->transaction_date = now()->format('Y-m-d');
        $this->showAddModal = true;
    }

    public function closeAddModal()
    {
        $this->reset([
            'category_id',
            'amount',
            'type',
            'description',
        ]);
        $this->resetErrorBag();
        $this->showAddModal = false;
    }

    public function editModal($id)
    {
        $this->selectedTransaction = Auth::user()->transaction()->findOrFail($id);

        $this->category_id = $this->selectedTransaction->category_id;
        $this->amount = $this->selectedTransaction->amount;
        $this->type = $this->selectedTransaction->type;
        $this->description = $this->selectedTransaction->description;
        $this->transaction_date = $this->selectedTransaction->transaction_date;

        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->reset([
            'category_id',
            'amount',
            'type',
            'description',
        ]);
        $this->resetErrorBag();
        $this->showEditModal = false;
    }


    // CRUD FUNCTIONS

};
?>

<div class="space-y-6">
    {{-- Breadcrumbs --}}
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('dashboard') }}">
            Home
        </flux:breadcrumbs.item>

        <flux:breadcrumbs.item>
            Transactions
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    {{-- Heading --}}
    <div class="flex flex-col md:flex-row justify-between space-y-4 border-b">
        <div>
            <flux:heading size="xl">
                Transactions
            </flux:heading>

            <flux:text>
                Track your daily income and expenses
            </flux:text>
        </div>

        <flux:button
            wire:click='addModal'
            icon="plus-circle"
            wire:loading.attr='disabled'
        >
            Create Transaction
        </flux:button>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

    </div>

    {{-- Filter --}}
    <div>

    </div>

    {{-- Main Section --}}

    {{-- Add Modal --}}
    @if ($showAddModal == true)
        <x-popup close="closeAddModal">
            <form wire:submit='addTransaction' class="space-y-6">
                <div class="flex flex-row justify-between items-center">
                    <flux:heading size="xl">
                        Create Transaction
                    </flux:heading>

                    <flux:button wire:click='closeAddModal'>
                        X
                    </flux:button>
                </div>

                <div class="space-y-6">
                    <div>
                        <flux:text class="mb-2">Type of Trancsaction</flux:text>
                        <flux:select wire:model='type' placeholder="Select type of transaction..." required>
                            <flux:select.option>Income</flux:select.option>
                            <flux:select.option>Expense</flux:select.option>
                        </flux:select>
                        @error('type')
                            <flux:text color="red">{{ $message }}</flux:text>
                        @enderror
                    </div>
                    <div>
                        <flux:text class="mb-2">Type of Category</flux:text>
                        <flux:input placeholder="Enter name of category..." wire:model='name' required/>
                        @error('name')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </form>
        </x-popup>
    @endif

</div>
