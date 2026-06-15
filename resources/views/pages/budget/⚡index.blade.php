<?php

use Livewire\Component;
use App\Models\Budget;

new class extends Component
{
    public $showAddModal = false;
    public $showEditModal = false;

    public $category_id = '';
    public $type = '';
    public $amount_limit = '';
    public $month = '';
    public $year = '';

    public ?Budget $selectedBudget = null;

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;
    }

    public function budgets()
    {
        return Auth::user()
            ->budget()
            ->with('category')
            ->where('month', $this->month)
            ->where('year', $this->year)
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
            'type',
            'amount_limit',
            'month',
            'year',
        ]);

        $this->showAddModal = false;
    }

    public function setBudget()
    {

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
            <x-modal close="closeAddModal">
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
                            <flux:select wire:model='category_id' palceholder="Select the type of category..." required>
                                @foreach ($this->categories() as $category)
                                    <flux:select.option value="{{ $category->id }}" class="capitalize">{{ $category->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>

                        <div>
                            <flux:text class="mb-2">Month</flux:text>
                            <flux:select wire:model.live='month'>
                                @foreach (range(now()->year - 1, now()->year + 100) as $year)
                                    <flux:select.option value="{{ $year }}">{{ $year }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>

                        <div>
                            <flux:text class="mb-2">Year</flux:text>
                            <flux:select wire:model.live='year'>
                                @foreach (range(now()->year - 1, now()->year + 50) as $year)
                                    <flux:select.option value="{{ $year }}">{{ $year }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>

                    </div>
                </form>
            </x-modal>
        @endif
    </div>
</div>
