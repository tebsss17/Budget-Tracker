<?php

use Livewire\Component;
use App\Models\Category;
use Flux\Flux;

new class extends Component
{
    public $name = '';
    public $type = '';
    public $selectedCategory = null;
    public $showAddModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;

    public $filteredType = '';


    // MODAL FUNCTIONS
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
        $this->resetErrorBag();
        $this->showAddModal = false;
    }

    public function closeEditModal()
    {
        $this->reset(['name', 'type']);
        $this->resetErrorBag();
        $this->showEditModal = false;
    }

    public function closeDeleteModal()
    {
        $this->reset(['name', 'type']);
        $this->showDeleteModal = false;
    }



    // CRUD FUNCTIONS
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

        Flux::toast(variant: 'success', text: 'Category Created Successfully!');

        $this->closeAddModal();

    }

    public function editCategory()
    {
        $validated = $this->validate([
            'type' => 'required',
            'name' => 'required|string|max:255'
        ]);

        $validated['name'] = strtolower(trim($validated['name']));

        $exists = auth()->user()->category()->where('name', $validated['name'])->where('id', '!=', $this->selectedCategory->id)->exists();

        if($exists)
        {
            $this->addError('name', 'Category already exists.');
            return;
        }

        $this->selectedCategory->update($validated);

        Flux::toast(variant: 'success', text: 'Category Edited Successfully!');

        $this->closeEditModal();
    }

    public function deleteCategory()
    {
        $this->selectedCategory->delete();

        Flux::toast(variant: 'success', text: 'Category Deleted Successfully!');

        $this->closeDeleteModal();
    }



    // QUERY FUNCTIONS
    public function categories()
    {
        return Auth::user()->category()
            ->when($this->filteredType, function($query){
                $query->where('type', $this->filteredType);
            })
            ->paginate(10);
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

        {{-- Filter --}}
        <div class="md:max-w-sm">
            <flux:select wire:model.live='filteredType'>
                <flux:select.option value="">All Types</flux:select.option>
                <flux:select.option value="Income">Income</flux:select.option>
                <flux:select.option value="Expense">Expense</flux:select.option>
            </flux:select>
        </div>

        {{-- Main --}}
        <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-4 gap-6 mt-20">
            @forelse ($this->categories() as $category)
                <flux:card wire:key='category-{{ $category->id }}'>
                    <p class="capitalize">{{ $category->name }}</p>
                    <p class="capitalize">{{ $category->type }}</p>
                    <div class="flex justify-end gap-2 mt-6">
                        <flux:button
                            wire:click='editModal({{ $category->id }})'
                            variant="filled"
                            icon="pencil-square"
                        >
                            Edit
                        </flux:button>

                        <flux:button
                            wire:click='deleteModal({{ $category->id }})'
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
                            <p class="text-zinc-500 italic text-sm">You have not created any categories. Add one</p>
                            <flux:button
                                wire:click='addModal'
                                icon="plus-circle"
                                wire:loading.attr='disabled'
                            >
                                Add Category
                            </flux:button>
                    </flux:card>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Add modal --}}
    @if ($showAddModal == true)
        <x-popup close="closeAddModal">
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
                            <flux:select.option>Expense</flux:select.option>
                        </flux:select>
                        @error('type')
                            <flux:text color="red">{{ $message }}</flux:text>
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
                        type="submit"
                    >
                        Create
                    </flux:button>
                </div>
            </form>
        </x-popup>
    @endif

    {{-- Edit modal --}}
    @if ($showEditModal == true)
        <x-popup close="closeEditModal">
            <form wire:submit='editCategory' class="space-y-6">
                <div class="flex flex-row justify-between items-center">
                    <flux:heading size="xl">
                        Edit Category - {{ $selectedCategory->name }}
                    </flux:heading>

                    <flux:button wire:click='closeEditModal'>
                        X
                    </flux:button>
                </div>

                <div class="space-y-6">
                    <div>
                        <flux:text class="mb-2">Type of Category</flux:text>
                        <flux:select wire:model='type' placeholder="Select type of category..." required>
                            <flux:select.option>Income</flux:select.option>
                            <flux:select.option>Expense</flux:select.option>
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
                        wire:click='closeEditModal'
                    >
                        Cancel
                    </flux:button>

                    <flux:button
                        type="submit"
                    >
                        Update
                    </flux:button>
                </div>
            </form>
        </x-popup>
    @endif

    {{-- Delete modal --}}
    @if ($showDeleteModal == true)
        <x-popup close="closeDeleteModal">
            <form wire:submit='deleteCategory' class="space-y-6">
                <div class="flex justify-between items-center">
                    <div>
                        <flux:heading size="xl">
                            Delete Category - {{ $selectedCategory->name }}
                        </flux:heading>
                        <flux:text>
                            Are you sure you want to delete this category?
                        </flux:text>
                    </div>
                        <flux:button wire:click='closeAddModal'>
                            X
                        </flux:button>

                </div>

                <div class="flex justify-end gap-2">
                    <flux:button
                        wire:click='closeDeleteModal'
                    >
                        Cancel
                    </flux:button>

                    <flux:button
                        type="submit"
                        variant="danger"
                    >
                        Delete
                    </flux:button>
                </div>
            </form>
        </x-popup>
    @endif

    <div class="mt-6">
        {{ $this->categories()->links() }}
    </div>
</div>
