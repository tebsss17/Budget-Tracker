<?php

use Livewire\Component;
use Flux\Flux;

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

        public $icons = [
            'smartphone',
            'laptop',
            'house',
            'plane',
            'car',
            'motorbike',
            'camera',
            'heart-pulse',
            'gem',
            'gift',
            'briefcase-business',
            'target',
        ];

    public $colors = [
        'red',
        'orange',
        'amber',
        'yellow',
        'emerald',
        'teal',
        'cyan',
        'sky',
        'blue',
        'purple',
        'pink',
    ];


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

    public function loadGoal($id)
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
        $this->loadGoal($id);

        $this->showEditModal = true;
    }

    public function deleteModal($id)
    {
        $this->loadGoal($id);

        $this->showDeleteModal = true;
    }


    // CRUD FUNCTIONS
    protected function rules()
    {
        return [
            'name' => 'required|string|min:5',
            'target_amount' => 'required|number|min:500',
            'target_date' => 'required|date',
            'icon' => 'required',
            'color' => 'required',
        ];
    }

    protected function messages()
    {

    }

    public function setGoal()
    {
        $validated = $this->validate();

        $validated['name'] = strtolower(trim($validated['name']));
        $validated['is_active'] = true;

        Auth::user()->goal()->create($validated);

        Flux::toast(variant: 'sucess', text: 'Goal Created Successfully!');

        $this->closeModals();
    }

    public function editGoal()
    {
        $validated = $this->validate();

        $validated['name'] = strtolower(trim($validated['name']));
        $validated['is_active'] = true;

        Auth::user()->goal()->update($validated);

        Flux::toast(variant: 'sucess', text: 'Goal Updated Successfully!');

        $this->closeModals();
    }

    public function deleteGoal()
    {

    }

    public function toggleIcon($icon)
    {
        $this->icon = $this->icon === $icon ? '' : $icon;
    }

    public function toggleColor($color)
    {
        $this->color = $this->color === $color ? '' : $color;
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
            wire:click='addModal'
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
        <x-popup close="closeModals">
            <form wire:submit='addCategory' class="space-y-6">
                <div class="flex flex-row justify-between items-center">
                    <flux:heading size="xl">
                        Set Goal
                    </flux:heading>

                    <flux:button wire:click='closeModals'>
                        X
                    </flux:button>
                </div>

                {{-- Goal Contents --}}
                <div class="space-y-6">

                    {{-- Name --}}
                    <div>
                        <flux:text class="mb-2">Name</flux:text>
                        <flux:input
                            placeholder="Goal Name"
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

                    {{-- Target Amount --}}
                    <div>
                        <flux:text class="mb-2">Target Amount</flux:text>
                        <flux:input
                            placeholder="₱500.00"
                            wire:model='taget_amount'
                            required
                            type="number"
                        />
                        <div>
                            @error('target_amount')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Target Date --}}
                    <div>
                        <flux:text class="mb-2">Target Date</flux:text>
                        <flux:input

                            wire:model='target_date'
                            required
                            type="month"
                        />
                        <div>
                            @error('target_amount')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Icon --}}
                    <div>
                        <flux:text class="mb-2">Choose an Icon</flux:text>

                        <div class="max-h-50 overflow-y-auto rounded-lg border p-2">
                            <div class="grid grid-cols-6  gap-2">
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
                                                    'emerald' => 'bg-emerald-600',
                                                    'teal' => 'bg-teal-600',
                                                    'cyan' => 'bg-cyan-600',
                                                    'sky' => 'bg-sky-600',
                                                    'blue' => 'bg-blue-600',
                                                    'purple' => 'bg-purple-600',
                                                    'pink' => 'bg-pink-600',
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
                            Set Goal
                        </flux:button>
                    </div>

                </div>
            </form>
        </x-popup>
    @endif

    {{-- Edit Modal --}}
    @if ($showEditModal === true)
        <x-popup close="closeModals">
            <form wire:submit='editCategory' class="space-y-6">
                <div class="flex flex-row justify-between items-center">
                    <flux:heading size="xl">
                        Edit Goal {{ $selectedGoal->name ?: 'Test' }}
                    </flux:heading>

                    <flux:button wire:click='closeModals'>
                        X
                    </flux:button>
                </div>

                {{-- Goal Contents --}}
                <div class="space-y-6">

                    {{-- Name --}}
                    <div>
                        <flux:text class="mb-2">Name</flux:text>
                        <flux:input
                            placeholder="Goal Name"
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

                    {{-- Target Amount --}}
                    <div>
                        <flux:text class="mb-2">Target Amount</flux:text>
                        <flux:input
                            placeholder="₱500.00"
                            wire:model='taget_amount'
                            required
                            type="number"
                        />
                        <div>
                            @error('target_amount')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Target Date --}}
                    <div>
                        <flux:text class="mb-2">Target Date</flux:text>
                        <flux:input

                            wire:model='target_date'
                            required
                            type="month"
                        />
                        <div>
                            @error('target_amount')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Icon --}}
                    <div>
                        <flux:text class="mb-2">Choose an Icon</flux:text>

                        <div class="max-h-50 overflow-y-auto rounded-lg border p-2">
                            <div class="grid grid-cols-6  gap-2">
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
                                                    'emerald' => 'bg-emerald-600',
                                                    'teal' => 'bg-teal-600',
                                                    'cyan' => 'bg-cyan-600',
                                                    'sky' => 'bg-sky-600',
                                                    'blue' => 'bg-blue-600',
                                                    'purple' => 'bg-purple-600',
                                                    'pink' => 'bg-pink-600',
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
                            Save Changes
                        </flux:button>
                    </div>

                </div>
            </form>
        </x-popup>
    @endif

    {{-- Delete Modal --}}
    @if ($showDeleteModal == true)
        <x-popup close="closeModals">
            <form wire:submit='deleteGoal' class="space-y-6">
                <div class="flex flex-row justify-between items-center">
                    <flux:heading size="xl">
                        Delete {{ $selectedGoal->name }} goal
                    </flux:heading>

                    <flux:button wire:click='closeModals'>
                        X
                    </flux:button>
                </div>

                <div>
                    <flux:text>
                        Are you sure you want to delete this goal?
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
                            Delete Goal
                        </flux:button>
                    </div>
                </div>
            </form>
        </x-popup>
    @endif
</div>
