<?php

use Livewire\Component;
use App\Models\Category;

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

    public $selectedCategory = null;

    public $filteredCategory = '';
    public $filteredType = '';



    // QUERY FUNCTIONS
    public function categories()
    {
        return Category::query()
            ->where(function($query) {
                $query->where('is_default', true)
                    ->orWhere('user_id', auth()->id());
            })

            ->when($this->filteredCategory === 'default', function ($query) {
                $query->where('is_default', true);
            })

            ->when($this->filteredCategory === 'custom', function ($query) {
                $query->where('user_id', auth()->id())
                      ->where('is_default', false);
            })

            ->when($this->filteredType, function($query)  {
                $query->where('type', $this->filteredType);
            })

            ->orderBy('type')
            ->orderBy('name')
            ->paginate(10);
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

    public function loadCategories($id)
    {
        $this->selectedCategory = Auth::user()
            ->category()
            ->findOrFail($id);

        $this->name  = $this->selectedCategory->name;
        $this->type  = $this->selectedCategory->type;
        $this->icon  = $this->selectedCategory->icon;
        $this->color  = $this->selectedCategory->color;
    }

    public function addModal()
    {
        $this->showAddModal = true;
    }

    public function editModal($id)
    {
        $this->loadCategories($id);
        $this->showEditModal = true;

    }

    public function deleteModal($id)
    {
        $this->loadCategories($id);
        $this->showDeleteModal = true;
    }


    // CRUD LOGIC
    public function addCategory()
    {

    }

    public function editCategory()
    {

    }

    public function deleteCategory()
    {

    }

    protected function rules()
    {

    }

    protected function messages()
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
                Manage your default and custom categories for organizing your transactions
            </flux:text>
        </div>

        <flux:button
            wire:click='addModal'
            icon="plus-circle"
            wire:loading.attr='disabled'
        >
            Create Category
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

    @if ($showAddModal == true)
        <x-popup close="closeModals">
            <form wire:submit='addCategory' class="space-y-6">
                <div class="flex flex-row justify-between items-center">
                    <flux:heading size="xl">
                        Create Custom Category
                    </flux:heading>

                    <flux:button wire:click='closeModals'>
                        X
                    </flux:button>
                </div>

                <div class="space-y-6">
                    <div>
                        <flux:text class="mb-2">Type of Category</flux:text>
                        <flux:select wire:model='type' placeholder="Select Type of Category..." required>
                            <flux:select.option>Income</flux:select.option>
                            <flux:select.option>Expense</flux:select.option>
                        </flux:select>
                        @error('type')
                            <flux:text color="red">{{ $message }}</flux:text>
                        @enderror
                    </div>

                    <div>
                        <flux:text class="mb-2">Name</flux:text>
                        <flux:input
                            placeholder="Name of Category"
                            wire:model='name'
                            required
                            type="text"
                        />
                        @error('name')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2">
                        <flux:button
                            wire:click='closeModals'
                        >
                            Cancel
                        </flux:button>

                        <flux:button
                            type="submit"
                            icon="plus-circle"
                        >
                            Create Category
                        </flux:button>
                    </div>

                </div>
            </form>
    </x-popup>
@endif

</div>

