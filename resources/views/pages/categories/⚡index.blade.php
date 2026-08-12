<?php

use Livewire\Component;
use App\Models\Category;
use Flux\Flux;

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

    public $filterCategory = '';
    public $filterType = '';

    public $icons = [
        'utensils-crossed',
        'car',
        'shopping-cart',
        'receipt-text',
        'house',
        'lightbulb',
        'heart-pulse',
        'pill',
        'dumbbell',
        'school',
        'book-open',
        'clapperboard',
        'plane',
        'fuel',
        'paw-print',
        'shirt',
        'gift',
        'coffee',
        'briefcase-business',
        'banknote',
        'store',
        'chart-spline',
        'wallet',
        'circle-question-mark',
    ];

    public $colors = [
        'red',
        'orange',
        'amber',
        'yellow',
        'lime',
        'green',
        'emerald',
        'teal',
        'cyan',
        'sky',
        'blue',
        'indigo',
        'violet',
        'purple',
        'pink',
        'zinc'
    ];



    // QUERY FUNCTIONS
    public function categories()
    {
        return Category::query()
            ->where(function($query) {
                $query->where('is_default', true)
                    ->orWhere('user_id', auth()->id());
            })

            ->when($this->filterCategory === 'default', function ($query) {
                $query->where('is_default', true);
            })

            ->when($this->filterCategory === 'custom', function ($query) {
                $query->where('is_default', 'false')
                    ->where('user_id', auth()->id());
            })

            ->when($this->filterType, function($query)  {
                $query->where('type', $this->filterType);
            })

            ->orderBy('type')
            ->orderBy('name')
            ->paginate(10);
    }



    // HELPERS FUCNTIONS
    public function toggleIcon($icon)
    {
        $this->icon = $this->icon === $icon ? '' : $icon;
    }

    public function toggleColor($color)
    {
        $this->color = $this->color === $color ? '' : $color;
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
        $this->selectedCategory = Category::where(function ($query) {
            $query->where('is_default', true)
                  ->orWhere('user_id', auth()->id());
        })
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
        $validated =  $this->validate();

        $validated['name'] = strtolower(trim($validated['name']));
        $validated['is_default'] = false;

        $exists = auth()->user()->category()->where('name', $validated['name'])->exists();

        if($exists)
        {
            $this->addError('name', 'Category already exists.');
            return;
        }

        Auth::user()->category()->create($validated);

        Flux::toast(variant: 'success', text: 'Category Created Successfully!');

        $this->closeModals();
    }

    public function editCategory()
    {
        $validated =  $this->validate();

        $validated['name'] = strtolower(trim($validated['name']));

        $exists = auth()->user()->category()->where('name', $validated['name'])->exists();

        if($exists)
        {
            $this->addError('name', 'Category already exists.');
            return;
        }

        $this->selectedCategory->update($validated);

        Flux::toast(variant: 'success', text: 'Category Updated Successfully!');

        $this->closeModals();
    }

    public function deleteCategory()
    {
        $category = $this->selectedCategory;

        if ($category->transaction()->exists()){
            Flux::toast(variant: 'danger', text: 'This category cannot be deleted because it has logs');
            $this->closeModals();
            return;
        }

        $category->delete();

        Flux::toast(variant: 'success', text: 'Category Deleted Successfully!');

        $this->closeModals();
    }

    protected function rules()
    {
        return [
            'name' => 'required|min:3',
            'type' => 'required|string',
            'icon' => 'required',
            'color' => 'required'
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'Please enter a category name.',
            'name.min' => 'Category name must be a least 3 characters.',

            'type.required' => 'Please select a category type.',

            'icon.required' => 'Please choose an icon',

            'color.required' => 'Please choose a color',

        ];
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

        <div class="md:max-w-sm md:flex-1">
            <flux:select wire:model.live='filterCategory' placeholder="All Types..." >
                <flux:select.option value="">All Types</flux:select.option>
                <flux:select.option value="default">Default</flux:select.option>
                <flux:select.option value="custom">Custom</flux:select.option>
            </flux:select>
        </div>
    </div>

    {{-- Main Section --}}
    <div class="mt-20">
        <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse ($this->categories() as $category )
                <flux:card wire:key='category-{{ $category->id }}'>
                    <p>{{ $category->type }}</p>

                    @if ($category->is_default)
                        <x-mary-badge class="bg-emerald-300">
                            Default
                        </x-mary-badge>
                    @else
                        <x-mary-badge>
                            Custom
                        </x-mary-badge>

                    @endif
                    <p class="capitalize">{{ $category->name }}</p>
                    <x-dynamic-component :component="'lucide-'.$category->icon" class="h-5 w-5"/>
                    <p>{{ $category->icon }}</p>
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
                                icon="trash"
                            >
                                Delete
                            </flux:button>
                        </div>
                </flux:card>
            @empty

            @endforelse
        </div>
    </div>

    {{-- Add modal --}}
    @if ($showAddModal)
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

                {{-- Category Type --}}
                <div class="space-y-6">
                    <div>
                        <div class="flex gap-2">
                            <button
                                type="button"
                                wire:click="$set('type', 'Income')"
                                class="flex-1 rounded-lg border px-4 py-2 text-sm font-black uppercase transition-all duration-300 ease-in-out
                                {{ $type === 'Income'
                                    ? 'border-emerald-500 bg-emerald-50 text-emerald-600 shadow-sm'
                                    : 'border-zinc-200 hover:border-zinc-300 hover:bg-zinc-50' }}"
                            >
                                Income
                            </button>

                            <button
                                type="button"
                                wire:click="$set('type', 'Expense')"
                                class="flex-1 rounded-lg border px-4 py-2 text-sm font-black uppercase transition-all duration-300 ease-in-out
                                {{ $type === 'Expense'
                                    ? 'border-rose-500 bg-rose-50 text-rose-600 shadow-sm'
                                    : 'border-zinc-200 hover:border-zinc-300 hover:bg-zinc-50' }}"
                            >
                                Expense
                            </button>
                        </div>

                        <div>
                            @error('type')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Name --}}
                    <div>
                        <flux:text class="mb-2">Name</flux:text>
                        <flux:input
                            placeholder="Name of Category"
                            wire:model='name'
                            required
                            type="text"
                        />
                        <div>
                            @error('name')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Icon --}}
                    <div>
                        <flux:text class="mb-2">Choose an Icon</flux:text>

                        <div class="max-h-50 overflow-y-auto rounded-lg border p-2">
                            <div class="grid grid-cols-6 lg:grid-cols-8 gap-2">
                                @foreach ($icons as $item )
                                    <button
                                        type="button"
                                        wire:click="toggleIcon('{{ $item }}')"
                                        class="flex aspect-square items-center justify-center rounded-lg border transition-all duration-300 {{ $icon ===  $item
                                            ? 'border-blue-500 bg-blue-100 text-blue-600 shadow-sm'
                                            : 'border-zinc-200 hover:border-zinc-300 hover:bg-zinc-50 scale-105'  }}"
                                    >
                                            <x-dynamic-component :component="'lucide-'.$item"
                                                class="size-4"
                                            />
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        @error('icon')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Color --}}
                    <div>
                        <flux:text class="mb-2">Choose a Color</flux:text>

                        <div class="max-h-50 overflow-y-auto rounded-lg border p-2">
                            <div class="grid grid-cols-6 lg:grid-cols-8 gap-2">
                                @foreach ($colors as $item )
                                    <button
                                        type="button"
                                        wire:click="toggleColor('{{ $item }}')"
                                        class="size-8 rounded-full border-2 transition-all duration-300
                                                {{ $color === $item ? 'ring-2 ring-offset-2 ring-zinc-400 scale-110' : 'hover:scale-105'}}"
                                    >
                                        <div
                                            class="size-full rounded-full
                                                {{ match($item) {
                                                    'red' => 'bg-red-600',
                                                    'orange' => 'bg-orange-600',
                                                    'amber' => 'bg-amber-600',
                                                     'yellow' => 'bg-yellow-600',
                                                    'lime' => 'bg-lime-600',
                                                    'green' => 'bg-green-600',
                                                    'emerald' => 'bg-emerald-600',
                                                    'teal' => 'bg-teal-600',
                                                    'cyan' => 'bg-cyan-600',
                                                    'sky' => 'bg-sky-600',
                                                    'blue' => 'bg-blue-600',
                                                    'indigo' => 'bg-indigo-600',
                                                    'violet' => 'bg-violet-600',
                                                    'purple' => 'bg-purple-600',
                                                    'pink' => 'bg-pink-600',
                                                    'zinc' => 'bg-zinc-600',
                                                } }}"
                                        >
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        @error('color')
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

    {{-- Edit modal --}}
    @if ($showEditModal)
        <x-popup close="closeModals">
            <form wire:submit='editCategory' class="space-y-6">
                <div class="flex flex-row justify-between items-center">
                    <flux:heading size="xl">
                        Edit Category {{ $selectedCategory->id }}
                    </flux:heading>

                    <flux:button wire:click='closeModals'>
                        X
                    </flux:button>
                </div>

                {{-- Category Type --}}
                <div class="space-y-6">
                    <div>
                        <div class="flex gap-2">
                            <button
                                type="button"
                                wire:click="$set('type', 'Income')"
                                class="flex-1 rounded-lg border px-4 py-2 text-sm font-black uppercase transition-all duration-300 ease-in-out
                                {{ $type === 'Income'
                                    ? 'border-emerald-500 bg-emerald-50 text-emerald-600 shadow-sm'
                                    : 'border-zinc-200 hover:border-zinc-300 hover:bg-zinc-50' }}"
                            >
                                Income
                            </button>

                            <button
                                type="button"
                                wire:click="$set('type', 'Expense')"
                                class="flex-1 rounded-lg border px-4 py-2 text-sm font-black uppercase transition-all duration-300 ease-in-out
                                {{ $type === 'Expense'
                                    ? 'border-rose-500 bg-rose-50 text-rose-600 shadow-sm'
                                    : 'border-zinc-200 hover:border-zinc-300 hover:bg-zinc-50' }}"
                            >
                                Expense
                            </button>
                        </div>

                        <div>
                            @error('type')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Name --}}
                    <div>
                        <flux:text class="mb-2">Name</flux:text>
                        <flux:input
                            placeholder="Name of Category"
                            wire:model='name'
                            required
                            type="text"
                        />
                        <div>
                            @error('name')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Icon --}}
                    <div>
                        <flux:text class="mb-2">Choose an Icon</flux:text>

                        <div class="max-h-50 overflow-y-auto rounded-lg border p-2">
                            <div class="grid grid-cols-6 lg:grid-cols-8 gap-2">
                                @foreach ($icons as $item )
                                    <button
                                        type="button"
                                        wire:click="toggleIcon('{{ $item }}')"
                                        class="flex aspect-square items-center justify-center rounded-lg border transition-all duration-300 {{ $icon ===  $item
                                            ? 'border-blue-500 bg-blue-100 text-blue-600 shadow-sm'
                                            : 'border-zinc-200 hover:border-zinc-300 hover:bg-zinc-50 scale-105'  }}"
                                    >
                                            <x-dynamic-component :component="'lucide-'.$item"
                                                class="size-4"
                                            />
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        @error('icon')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Color --}}
                    <div>
                        <flux:text class="mb-2">Choose a Color</flux:text>

                        <div class="max-h-50 overflow-y-auto rounded-lg border p-2">
                            <div class="grid grid-cols-6 lg:grid-cols-8 gap-2">
                                @foreach ($colors as $item )
                                    <button
                                        type="button"
                                        wire:click="toggleColor('{{ $item }}')"
                                        class="size-8 rounded-full border-2 transition-all duration-300
                                                {{ $color === $item ? 'ring-2 ring-offset-2 ring-zinc-400 scale-110' : 'hover:scale-105'}}"
                                    >
                                        <div
                                            class="size-full rounded-full
                                                {{ match($item) {
                                                    'red' => 'bg-red-600',
                                                    'orange' => 'bg-orange-600',
                                                    'amber' => 'bg-amber-600',
                                                     'yellow' => 'bg-yellow-600',
                                                    'lime' => 'bg-lime-600',
                                                    'green' => 'bg-green-600',
                                                    'emerald' => 'bg-emerald-600',
                                                    'teal' => 'bg-teal-600',
                                                    'cyan' => 'bg-cyan-600',
                                                    'sky' => 'bg-sky-600',
                                                    'blue' => 'bg-blue-600',
                                                    'indigo' => 'bg-indigo-600',
                                                    'violet' => 'bg-violet-600',
                                                    'purple' => 'bg-purple-600',
                                                    'pink' => 'bg-pink-600',
                                                    default => 'bg-zinc-600',
                                                } }}"
                                        >
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        @error('color')
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
                            wire:loading.attr='disabled'
                        >
                            Save Changes
                        </flux:button>
                    </div>

                </div>
            </form>
        </x-popup>
    @endif

    {{-- Delete modal --}}
    @if ($showDeleteModal)
        <x-popup close="closeModals">
            <form wire:submit='deleteCategory' class="space-y-6">
            <div class="flex flex-row justify-between items-center">
                    <flux:heading size="xl">
                        Delete {{ $selectedCategory->name }} budget
                    </flux:heading>

                    <flux:button wire:click='closeModals'>
                        X
                    </flux:button>
                </div>

                <div>
                    <flux:text>
                        Are you sure you want to delete this budget?
                    </flux:text>
                </div>

                <div class="space-y-6">
                    <div class="flex justify-end gap-2">
                        <flux:button
                            wire:click='closeModals'
                        >
                            Cancel
                        </flux:button>

                        <flux:button
                            type="submit"
                            wire:loading.attr='disabled'
                        >
                            Delete Category
                        </flux:button>
                    </div>
                </div>
            </form>
        </x-popup>
    @endif

    <div class="mt-6">
        {{ $this->categories()->links() }}
    </div>

</div>

