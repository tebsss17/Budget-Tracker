<?php

use Livewire\Component;
use App\Models\Budget;

new class extends Component
{
    public $showAddModal = false;
    public $showEditModal = false;

    public $category_id = '';
    public $amount_limit = '';
    public $month = '';
    public $year = '';
    public $selectedDate = '';

    public ?Budget $selectedBudget = null;

    public function mount()
    {
        $this->selectedDate = now()->format('Y-m');
    }

    public function budgets()
    {
        $split = explode('-', $this->selectedDate);

        return Auth::user()
            ->budget()
            ->with('category')
            ->where('month', (int)$split[1])
            ->where('year', $split[0])
            ->get();
    }

    public function categories()
    {
        return Auth::user()->category()->orderBy('name')->get();
    }

    public function addModal()
    {
        $this->showAddModal = true;
    }

    public function closeAddModal()
    {
        $this->reset([
            'category_id',
            'selectedDate',
            'amount_limit',
        ]);
        $this->resetErrorBag();
        $this->showAddModal = false;
    }

    public function setBudget()
    {
        $this->validate([
            'category_id' => 'required',
            'amount_limit' => 'required|min:50|numeric',
            'selectedDate' => 'required',
        ]);

        $split = explode('-', $this->selectedDate);
        $year = $split[0];
        $month = (int)$split[1];

        $exists = Auth::user()->budget()
            ->where('category_id', $this->category_id)
            ->where('month', $month)
            ->where('year', $year)
            ->exists();

            if($exists)
            {
                $this->addError('selectedDate', 'Budget already set for this period.');
                return;
            }

        Auth::user()
            ->budget()
            ->create([
                'category_id' => $this->category_id,
                'amount_limit' => $this->amount_limit,
                'year' => $year,
                'month' => $month,
            ]);

        $this->closeAddModal();
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
            Budgets
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    {{-- Heading --}}
    <div class="flex flex-col md:flex-row justify-between space-y-4 border-b">
        <div>
            <flux:heading size="xl">
                Budgets
            </flux:heading>

            <flux:text>
                Set your monthly budgets to reduce overspending.
            </flux:text>
        </div>

        <flux:button
            wire:click='addModal'
            icon="plus-circle"
            wire:loading.attr='disabled'
        >
            Set Budget
        </flux:button>
    </div>

    {{-- Main section --}}
    <div>
        {{-- Filters --}}
        <div>

        </div>

        {{-- List of budgets --}}
        <div>
            <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse ($this->budgets() as $budget)
                    <flux:card wire:key='budget-{{ $budget->id }}'>
                        <p class="capitalize">{{ $budget->category->name }}</p>
                        <p class="capitalize">{{ date('F Y', mktime(0, 0, 0, $budget->month, 1, $budget->year)) }}</p>
                        <p class="capitalize">{{ $budget->amount_limit }}</p>
                        <div class="flex justify-end gap-2 mt-6">
                            <flux:button
                                wire:click='editModal({{ $budget->id }})'
                                variant="filled"
                                icon="pencil-square"
                            >
                                Edit
                            </flux:button>

                            <flux:button
                                wire:click='deleteModal({{ $budget->id }})'
                                variant="danger"
                                icon="trash"
                            >
                                Delete
                            </flux:button>
                        </div>
                    </flux:card>
                @empty
                    <div class="col-span-1 md:col-span-3 xl:col-span-4">
                        <flux:card class="flex flex-col items-center justify-center p-8 text-center gap-3 w-full">
                                <p class="text-zinc-500 italic text-sm">You have not set a budget yet. Add one</p>
                                <flux:button
                                    wire:click='addModal'
                                    icon="plus-circle"
                                    wire:loading.attr='disabled'
                                >
                                    Set Budget
                                </flux:button>
                        </flux:card>
                    </div>
                @endforelse
            </div>
        </div>

        @if ($showAddModal == true)
            <x-popup close="closeAddModal">
                <form wire:submit='setBudget' class="space-y-6">
                    <div class="flex flex-row justify-between items-center">
                        <flux:heading size="xl">
                            Set Budget
                        </flux:heading>

                        <flux:button wire:click='closeAddModal'>
                            X
                        </flux:button>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <flux:text class="mb-2">Category</flux:text>
                            <flux:select wire:model='category_id' placeholder="Select the type of category..." required>
                                @foreach ($this->categories() as $category)
                                    <flux:select.option value="{{ $category->id }}" class="capitalize">{{ $category->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            @error('category_id')
                                <p class="text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <flux:text class="mb-2">Selected Date</flux:text>
                            <flux:input
                                wire:model.live='selectedDate'
                                type="month"
                                required
                            />
                            @error('selectedDate')
                                <p class="text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <flux:text class="mb-2">Amount</flux:text>
                            <flux:input
                                wire:model='amount_limit'
                                required
                                type="number"
                                step="0.01"
                            />
                            @error('amount_limit')
                                <p class="text-sm text-red-500">{{ $message }}</p>
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
                            >
                                Set Budget
                            </flux:button>
                        </div>
                    </div>
                </form>
            </x-popup>
        @endif
    </div>
</div>
