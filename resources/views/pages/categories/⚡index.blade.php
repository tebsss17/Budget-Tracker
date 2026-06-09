<?php

use Livewire\Component;
use App\Models\Category;

new class extends Component
{
    public $name = '';
    public $type = '';
    public $selectedCategory = null;
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
        $this->selectedCategory = Category::findOrFail($id);
        $this->type = $this->selectedCategory->type;
        $this->name = $this->selectedCategory->name;
    }

    public function deleteModal($id)
    {
        $this->showDeleteModal = true;
        $this->selectedCategory = Category::findOrFail($id);
        $this->type = $this->selectedCategory->type;
        $this->name = $this->selectedCategory->name;
    }

    public function closeAddModal()
    {
        $this->reset(['name', 'type']);
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

    public function addCategory()
    {
        $validated = $this->validate([
            'type' => 'required',
            'name' => 'required|string|max:255'
        ]);

        $validated['name'] = strtolower(trim($validated['name']));

        $exists = auth()->user()->category()->where('name', $validated['name'])->exists();

        if($exists)
        {
            $this->addError('name', 'Category already exists.');
            return;
        }

        auth()->user()->category()->create($validated);

        $this->closeAddModal();

    }

    public function editCategory()
    {

    }

    public function deleteCategory()
    {

    }

    public function categories()
    {
        return auth()->user()->category;
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
    @if ($showAddModal == true)
        <x-modal close="closeAddModal">
            <form wire:submit='addCategory' class="space-y-6">
                <div class="flex flex-row justify-between items-center">
                    <flux:heading size="xl">
                        Add Category
                    </flux:heading>

                    <flux:button wire:click='closeAddModal'>
                        X
                    </flux:button>
                </div>

                <div class="space-y-6">
                    <div>
                        <flux:text class="mb-2">Type of Category</flux:text>
                        <flux:select wire:model='type' placeholder="Select type of category..." required>
                            <flux:select.option>Income</flux:select.option>
                            <flux:select.option>Expenses</flux:select.option>
                        </flux:select>
                        @error('type')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <flux:text class="mb-2">Name of Category</flux:text>
                        <flux:input placeholder="Enter name of category..." wire:model='name' required/>
                        @error('name')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <flux:button
                    wire:click='closeAddModal'
                    >
                        Cancel
                    </flux:button>

                    <flux:button
                    wire:click='addCategory'
                    >
                        Create
                    </flux:button>


                </div>


            </form>

        </x-modal>
    @endif
</div>
