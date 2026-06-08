<?php

use Livewire\Component;

new class extends Component
{
    public $name = '';
    public $type = '';
    public $showAddModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;

    public function addModal()
    {
        $this->showAddModal = true;
    }

    public function editModal($id)
    {
        $this->showEditModal = true;
    }

    public function deleteModal($id)
    {
        $this->showDeleteModal = true;
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
    }

    public function closeEditModal()
    {
        $this->reset(['name', 'type']);
        $this->showEditModal = false;
    }

    public function closeDeleteModal()
    {
        $this->reset(['name', 'type']);
        $this->showDeleteModal = false;
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
            Categories
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    {{-- Heading --}}
    <div class="flex flex-col md:flex-row justify-between space-y-4 border-b">
        <div>
            <flux:heading size="xl">
                Categories
            </flux:heading>

            <flux:text>
                Organize your income and expense transactions using categories.
            </flux:text>
        </div>

        <flux:button
            wire:click='addModal'
            icon="plus-circle"
            wire:loading.attr='disabled'
        >
            Add Category
        </flux:button>
    </div>

    {{-- Main Section --}}
    <div>

    </div>

    {{-- Add modal --}}
    @if ($showAddModal === true)
        <x-modal close="addModal">
            <form wire:submit='addCategory'>

            </form>

        </x-modal>
    @endif
</div>
