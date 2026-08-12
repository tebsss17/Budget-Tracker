<?php

use Livewire\Component;

new class extends Component
{
    // VARIABLES
    public $showAddModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;

    public $name;
    public $target_amount;
    public $current_amount;
    public $target_date;
    public $description;
    public $icon;
    public $color;

    public $selectedGoal = null;

    public $fliterStatus = '';
    public $filterYear = '';


    // QUERY FUNCTIONS
    public function goals()
    {
        return Auth::user()
            ->goal()
            ->when($this->flitertatus, function($query){
                $query->where('is_active', $this->fliterStatus);
            })
            ->when($this->filterYear, function($query){
                $query->where('target_date', $this->fliterYear);
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10);
    }

    public function years()
    {
        return Auth::user()
            ->goal()
            ->selectRaw('EXTRACT(YEAR FROM target_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
    }


    // Modal FUNCTIONS
    public function closeModals()
    {
        $this->showAddModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;

        $this->resetForms();

    }

    protected function resetForms()
    {
        $this->reset([
            'name',
            'target_amount',
            'current_amount',
            'description',
            'selectedGoal',
            'icon',
            'color',

        ]);

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function loadGoals($id)
    {
        $this->selectedGoal = Auth::user()
            ->goal()
            ->findOrFail($id);

        $this->name = $this->selectedGoal->name;
        $this->target_amount = $this->selectedGoal->target_amount;
        $this->current_amount = $this->selectedGoal->current_amount;
        $this->description = $this->selectedGoal->description;
        $this->icon = $this->selectedGoal->icon;
        $this->color = $this->selectedGoal->color;

    }

    public function addModal()
    {
        $this->showAddModal = true;
    }

    public function updateModal($id)
    {
        $this->loadGoals($id);

        $this->showEditModal = true;
    }

    public function deleteModal($id)
    {
        $this->loadGoals($id);

        $this->showDeleteModal = true;
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

        <flux:breadcrumbs.item href="{{ route('dashboard') }}">
            Goals
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    {{-- Heading --}}
    <div class="flex flex-col md:flex-row justify-between space-y-4 border-b">
        <div>
            <flux:heading size="xl">
                Goals
            </flux:heading>

            <flux:text>
                Plan, track, and achieve your financial goals
            </flux:text>
        </div>

        <flux:button
            wire:click='setGoal'
            icon="plus-circle"
            wire:loading.attr='disabled'
        >
            Set Goal
        </flux:button>
    </div>

    {{-- Filter --}}
    <div class="flex flex-col md:flex-row gap-4">
        <div class="md:max-w-sm md:flex-1">
            <flux:select wire:model.live='filterStatus' placeholder="Status..." >
                <flux:select.option value="">All Types</flux:select.option>
                <flux:select.option value="Income">Completed</flux:select.option>
                <flux:select.option value="Expense">Incomplete</flux:select.option>
            </flux:select>
        </div>

        <div class="md:max-w-sm md:flex-1">
            <flux:select wire:model.live='filterYear' placeholder="Year..." >
                <flux:select.option value="">All Time</flux:select.option>

            </flux:select>

        </div>
    </div>

    {{-- Add Modal --}}
    @if ($showAddModal === true)
        <x-popup>

        </x-popup>
    @endif
</div>
