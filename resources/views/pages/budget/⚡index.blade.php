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

    public function openAddModal()
    {
        $this->category_id = '';
        $this->amount_limit = '';
        $this->showAddModal = true;
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

        $this->showAddModal = false;
    }

    public function setBudget()
    {
        $validated = $this->validate([
            'category_id' => 'required|'
        ]);

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
                        </div>

                        <div>
                            <flux:text class="mb-2">Selected Date</flux:text>
                            <flux:input
                                wire:model.live='selectedDate'
                                type="month"
                                required
                            />
                        </div>

                        <div>
                            <flux:text class="mb-2">Amount</flux:text>
                            <flux:input
                                wire:model='amount_limit'
                                required
                                type="number"
                                step="0.01"
                            />
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
