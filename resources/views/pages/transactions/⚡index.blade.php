<?php

use Livewire\Component;
use Flux\Flux;

new class extends Component
{

    // VARIABLES
    public $showAddModal = false;
    public $showEditModal = false;
    public $showViewModal = false;

    public $category_id = '';
    public $amount = '';
    public $type = 'Expense';
    public $description = '';
    public $transaction_date = '';

    public $selectedTransaction = '';

    public $filterCategory = '';
    public $filterType = '';

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
            ->when($this->filterType, function($query){
                $query->where('type', $this->filterType);
            })
            ->when($this->filterCategory, function($query){
                $query->where('category_id', $this->filterCategory);
            })
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function categories()
    {
        return Auth::user()->category()->orderBy('name')->get();
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

    public function viewModal($id)
    {
        $this->selectedTransaction = Auth::user()->transaction()->findOrFail($id);

        $this->category_id = $this->selectedTransaction->category_id;
        $this->amount = $this->selectedTransaction->amount;
        $this->type = $this->selectedTransaction->type;
        $this->description = $this->selectedTransaction->description;
        $this->transaction_date = $this->selectedTransaction->transaction_date;

        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->reset([
            'category_id',
            'amount',
            'type',
            'description',
        ]);
        $this->resetErrorBag();
        $this->showViewModal = false;
    }


    // CRUD FUNCTIONS
    public function addTransaction()
    {
        $validated = $this->validate([
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|string',
            'amount' => 'required|numeric|min:50',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['transaction_date'] = $this->transaction_date;

        Auth::user()->transaction()->create($validated);

        Flux::toast(variant: 'success', text: 'Transaction Created Successfully!');

        $this->closeAddModal();

    }

    public function editTransaction()
    {
        $validated = $this->validate([
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|string',
            'amount' => 'required|numeric|min:50',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['transaction_date'] = $this->transaction_date;

        $this->selectedTransaction->update($validated);

        Flux::toast(variant: 'success', text: 'Transaction Edited Successfully!');

        $this->closeEditModal();


    }
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

    {{-- Filter --}}
    <div class="flex flex-col md:flex-row gap-4">
        <div class="md:max-w-sm md:flex-1">
            <flux:select wire:model.live='filterType' placeholder="All Types..." >
                <flux:select.option value="">All Types</flux:select.option>
                <flux:select.option value="Income">Income</flux:select.option>
                <flux:select.option value="Expense">Expense</flux:select.option>
            </flux:select>
        </div>

        <div class="md:max-w-sm md:flex-1">
            <flux:select wire:model.live='filterCategory' placeholder="All Categories..." >
                <flux:select.option value="">All Categories</flux:select.option>
                @foreach ($this->categories() as $category)
                    <flux:select.option value="{{ $category->id }}" class="capitalize">{{ $category->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    {{-- Main Section --}}
    <div class="mt-20">
        <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse ($this->transactions() as $transaction )
                <flux:card wire:key='transaction-{{ $transaction->id }}'>
                    <p>{{ $transaction->type }}</p>
                    <p class="capitalize">{{ $transaction->category->name }}</p>
                    <p>{{ $transaction->amount }}</p>
                    <div class="flex justify-end gap-2 mt-6">
                            <flux:button
                                wire:click='viewModal({{ $transaction->id }})'
                                variant="filled"
                                icon="eye"
                            >
                                Show
                            </flux:button>

                            <flux:button
                                wire:click='editModal({{ $transaction->id }})'
                                icon="pencil-square"
                            >
                                Edit
                            </flux:button>
                        </div>
                </flux:card>
            @empty

            @endforelse
        </div>
    </div>

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
                        <flux:select
                            placeholder="Select name of category..."
                            wire:model='category_id'
                            required
                        >
                            @foreach ( $this->categories() as $category )
                                <flux:select.option value="{{ $category->id }}" class="capitalize">{{ $category->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        @error('category_id')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <flux:text class="mb-2">Amount</flux:text>
                        <flux:input
                            placeholder="Enter amount..."
                            wire:model='amount'
                            required
                            type="number"
                        />
                        @error('amount')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex flex-row items-center mb-2 gap-2">
                            <flux:text>Note<flux:badge size="sm" color="zinc" rounded>optional</flux:badge></flux:text>
                        </div>
                        <flux:textarea
                            placeholder="Enter notes..."
                            wire:model='description'
                        />
                        @error('description')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2">
                        <flux:button
                            wire:click='closeAddModal'
                        >
                            Cancel
                        </flux:button>

                        <flux:button
                            type="submit"
                            icon="plus-circle"
                        >
                            Create Transaction
                        </flux:button>
                    </div>

                </div>
            </form>
        </x-popup>
    @endif

    {{-- Edit Modal --}}
    @if ($showEditModal == true)
        <x-popup close="closeEditModal">
            <form wire:submit='editTransaction' class="space-y-6">
                <div class="flex flex-row justify-between items-center">
                    <flux:heading size="xl">
                        Update Transaction no. {{ $selectedTransaction->id }}
                    </flux:heading>

                    <flux:button wire:click='closeEditModal'>
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
                        <flux:select
                            placeholder="Select name of category..."
                            wire:model='category_id'
                            required
                        >
                            @foreach ( $this->categories() as $category )
                                <flux:select.option value="{{ $category->id }}" class="capitalize">{{ $category->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        @error('category_id')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <flux:text class="mb-2">Amount</flux:text>
                        <flux:input
                            placeholder="Enter amount..."
                            wire:model='amount'
                            required
                            type="number"
                        />
                        @error('amount')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex flex-row items-center mb-2 gap-2">
                            <flux:text>Note<flux:badge size="sm" color="zinc" rounded>optional</flux:badge></flux:text>
                        </div>
                        <flux:textarea
                            placeholder="Enter notes..."
                            wire:model='description'
                        />
                        @error('description')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2">
                        <flux:button
                            wire:click='closeEditModal'
                        >
                            Cancel
                        </flux:button>

                        <flux:button
                            type="submit"
                            icon="plus-circle"
                        >
                            Update Transaction
                        </flux:button>
                    </div>

                </div>
            </form>
        </x-popup>
    @endif

    {{-- View Modal --}}
    @if ($showViewModal == true)
        <x-popup close="closeViewModal">
            <form  class="space-y-6">
                <div class="flex flex-row justify-between items-center">
                    <flux:heading size="xl">
                        View Transaction no. {{ $selectedTransaction->id }}
                    </flux:heading>

                    <flux:button wire:click='closeViewModal'>
                        X
                    </flux:button>
                </div>

                <div class="space-y-6">
                    <div>
                        <p>{{ $selectedTransaction->category->name }}</p>
                    </div>

                    <div>
                        {{ $amount }}
                    </div>

                    <div>
                        {{ date('m-d-Y', strtotime($transaction_date))  }}
                    </div>

                    <div>

                    </div>

                    <div class="flex justify-end gap-2">
                        <flux:button
                            wire:click='closeViewModal'
                        >
                            Cancel
                        </flux:button>

                        <flux:button
                            type="submit"
                            icon="plus-circle"
                        >
                            Create Transaction
                        </flux:button>
                    </div>

                </div>
            </form>
        </x-popup>
    @endif


</div>
