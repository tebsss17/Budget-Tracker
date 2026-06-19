<?php

use Livewire\Component;

new class extends Component
{
    public $showAddModal = false;
    public $showEditModal = false;

    public $category_id = '';
    public $amount = '';
    public $type = '';
    public $description = '';
    public $date = '';



    public function addModal()
    {
        $this->showAddModal = true;
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

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-card>

        </x-card>
    </div>

</div>
