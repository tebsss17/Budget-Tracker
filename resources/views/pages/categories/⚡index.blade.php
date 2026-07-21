<?php

use Livewire\Component;

new class extends Component
{
    // VARIABLES
    public $showAddModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;

    public $name = '';
    public $type = '';
    public $icon = '';
    public $color = '';

    public $category_id = null;

    public $filteredCategory = '';



    // QUERY FUNCTIONS
    public function categories()
    {

    }



    // MODAL FUNCTIONS
    public function resetForm()
    {
        $this->reset(
            [
                'name',
                'type',
                'icon',
                'color',
            ]
        );

        $this->resetErrorBag();
    }

    public function closeModals()
    {
        $this->showAddModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;

        $this->resetValidation();
        $this->resetForm();
    }



    // CRUD LOGIC
};
?>

<div>
    {{-- Breadcrumbs --}}
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('dashboard') }}">
            Home
        </flux:breadcrumbs.item>

        <flux:breadcrumbs.item>
            Categories
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    {{-- Heading --}}
    <div class="flex flex-col md:flex-row justify-between space-y-4 border-b">
        <div>
            <flux:heading size="xl">
                Categoriesd
            </flux:heading>

            <flux:text>
                Create your custom categories
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
    </div>
</div>
