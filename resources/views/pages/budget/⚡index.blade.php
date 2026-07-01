<?php

use Livewire\Component;
use App\Models\Budget;
use Flux\Flux;
use App\Models\Category;
new class extends Component
{
    // VARIABLES
    public $showAddModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;

    public $category_id = '';
    public $amount_limit = '';

    public $selectedDate = '';
    public $selectedBudget = null;
    public $editDate ='';

    public $filteredMonth = '';
    public $filteredYear = '';



    // QUERY FUNCTIONS
    public function mount()
    {
        $this->selectedDate = now()->format('Y-m');
    }

    public function budgets()
    {
        return Auth::user()
            ->budget()
            ->with('category')
            ->when($this->filteredMonth, function($query){
                $query->where('month', $this->filteredMonth);
            })
            ->when($this->filteredYear, function($query){
                $query->where('year', $this->filteredYear);
            })
            ->orderBy('month', 'desc')
            ->orderBy('year', 'desc')
            ->paginate(10);
    }

    public function categories()
    {
        return Category::orderBy('name')->get();
    }

    public function years()
    {
        return Auth::user()
            ->budget()
            ->select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
    }



    // MODAL FUNCTIONS
    public function addModal()
    {
        $this->showAddModal = true;
    }

    public function closeAddModal()
    {
        $this->reset([
            'category_id',
            'amount_limit',
        ]);

        $this->selectedDate = now()->format('Y-m');
        $this->resetErrorBag();
        $this->showAddModal = false;
    }

    public function editModal($id)
    {
        $this->selectedBudget = Budget::findOrFail($id);

        $formattedDate = str_pad($this->selectedBudget->month, 2, 0, STR_PAD_LEFT);

        $this->category_id = $this->selectedBudget->category_id;
        $this->amount_limit = $this->selectedBudget->amount_limit;
        $this->editDate = "{$this->selectedBudget->year}-{$formattedDate}";

        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->reset([
            'category_id',
            'amount_limit'
        ]);

        $this->selectedDate = now()->format('Y-m');
        $this->resetErrorBag();
        $this->showEditModal = false;
    }

    public function deleteModal($id)
    {
        $this->selectedBudget = Budget::findOrFail($id);

        $formattedDate = str_pad($this->selectedBudget->month, 2, 0, STR_PAD_LEFT);

        $this->category_id = $this->selectedBudget->category_id;
        $this->amount_limit = $this->selectedBudget->amount_limit;
        $this->editDate = "{$this->selectedBudget->year}-{$formattedDate}";

        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->reset([
            'category_id',
            'amount_limit'
        ]);

        $this->showDeleteModal = false;
    }



    // CRUD LOGIC
    public function setBudget()
    {
        $this->validate([
            'category_id' => 'required',
            'amount_limit' => 'required|min:50|numeric',
            'selectedDate' => 'required',
        ]);

        $split = explode('-', $this->selectedDate);
        $year = $split[0];
        $month = (int)$split[1];

        $exists = Auth::user()->budget()
            ->where('category_id', $this->category_id)
            ->where('month', $month)
            ->where('year', $year)
            ->exists();

            if($exists)
            {
                $this->addError('selectedDate', 'Budget already set for this period.');
                return;
            }

        Auth::user()
            ->budget()
            ->create([
                'category_id' => $this->category_id,
                'amount_limit' => $this->amount_limit,
                'year' => $year,
                'month' => $month,
            ]);

        Flux::toast(variant: 'success', text: 'Budget Created successfully!');

        $this->closeAddModal();
    }

    public function updateBudget()
    {
        $this->validate([
            'category_id' => 'required',
            'amount_limit' => 'required|numeric|min:50',
            'editDate' => 'required',
        ]);

        $split = explode('-', $this->editDate);
        $year = $split[0];
        $month = (int)$split[1];

        $exists = Auth::user()->budget()
            ->where('category_id', $this->category_id)
            ->where('month', $month)
            ->where('year', $year)
            ->where('id', '!=' , $this->selectedBudget->id)
            ->exists();

            if($exists)
            {
                $this->addError('editDate', 'Budget already set for this period.');
                return;
            }

        $this->selectedBudget->update([
            'category_id' => $this->category_id,
            'amount_limit' => $this->amount_limit,
            'year' => $year,
            'month' => $month,
        ]);

        Flux::toast(variant: 'success', heading: 'lets g', text: 'Budget Edited Successfully!');

        $this->closeEditModal();
    }

    public function deleteBudget()
    {
        $this->selectedBudget->delete();

        Flux::toast(variant: 'success', text: 'Budget Deleted Successfully!');

        $this->closeDeleteModal();
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
        <div class="flex md:flex-row flex-col gap-4">
            <div class="md:max-w-sm md:flex-1">
                <flux:select wire:model.live='filteredMonth'>
                    <flux:select.option value="">All Months</flux:select.option>
                        @foreach (range(1,12) as $month)
                            <flux:select.option value="{{ $month }}">
                                {{ date('F', mktime(0,0,0,$month,1)) }}
                            </flux:select.option>
                        @endforeach
                </flux:select>
            </div>

            <div class="md:max-w-sm md:flex-1">
                <flux:select wire:model.live='filteredYear'>
                    <flux:select.option value="">All Years</flux:select.option>
                    @foreach ($this->years() as $year)
                        <flux:select.option value="{{ $year }}">{{ $year }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        {{-- Main Section --}}
        <div class="mt-20">
            <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse ($this->budgets() as $budget)
                    <flux:card wire:key='budget-{{ $budget->id }}'>
                        <p class="capitalize">{{ $budget->category->name }}</p>
                        <p class="capitalize">{{ date('F Y', mktime(0, 0, 0, $budget->month, 1, $budget->year)) }}</p>
                        <p class="capitalize">{{ $budget->amount_limit }}</p>
                        <div class="flex justify-end gap-2 mt-6">
                            <flux:button
                                wire:click='editModal({{ $budget->id }})'
                                variant="filled"
                                icon="pencil-square"
                            >
                                Edit
                            </flux:button>

                            <flux:button
                                wire:click='deleteModal({{ $budget->id }})'
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
                                <p class="text-zinc-500 italic text-sm">You have not set a budget yet. Add one</p>
                                <flux:button
                                    wire:click='addModal'
                                    icon="plus-circle"
                                    wire:loading.attr='disabled'
                                >
                                    Set Budget
                                </flux:button>
                        </flux:card>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    @if ($showAddModal == true)
        <x-popup close="closeAddModal">
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
                        <flux:select wire:model='category_id' placeholder="Select the type of category..." required>
                            @foreach ($this->categories() as $category)
                                <flux:select.option value="{{ $category->id }}" class="capitalize">{{ $category->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        @error('category_id')
                            <p class="text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <flux:text class="mb-2">Selected Date</flux:text>
                        <flux:input
                            wire:model='selectedDate'
                            type="month"
                            required
                        />
                        @error('selectedDate')
                            <p class="text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <flux:text class="mb-2">Amount</flux:text>
                        <flux:input
                            wire:model='amount_limit'
                            required
                            type="number"
                            step="0.01"
                        />
                        @error('amount_limit')
                            <p class="text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2">
                        <flux:button
                            wire:click='closeAddModal'
                        >
                            Cancel
                        </flux:button>

                        <flux:button
                            type="submit"
                            wire:loading.attr='disabled'
                        >
                            Set Budget
                        </flux:button>
                    </div>
                </div>
            </form>
        </x-popup>
    @endif

    {{-- Edit Modal --}}
    @if ($showEditModal == true)
        <x-popup close="closeEditModal">
            <form wire:submit='updateBudget' class="space-y-6">
                <div class="flex flex-row justify-between items-center">
                    <flux:heading size="xl">
                        Update {{ $selectedBudget->category->name }} budget
                    </flux:heading>

                    <flux:button wire:click='closeEditModal'>
                        X
                    </flux:button>
                </div>

                <div class="space-y-6">
                    <div>
                        <flux:text class="mb-2">Category</flux:text>
                        <flux:select wire:model='category_id' placeholder="Select the type of category..." required>
                            @foreach ($this->categories() as $category)
                                <flux:select.option value="{{ $category->id }}" class="capitalize">{{ $category->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        @error('category_id')
                            <p class="text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <flux:text class="mb-2">Selected Date</flux:text>
                        <flux:input
                            wire:model='editDate'
                            type="month"
                            required
                        />
                        @error('editDate')
                            <p class="text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <flux:text class="mb-2">Amount</flux:text>
                        <flux:input
                            wire:model='amount_limit'
                            required
                            type="number"
                            step="0.01"
                        />
                        @error('amount_limit')
                            <p class="text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2">
                        <flux:button
                            wire:click='closeAddModal'
                        >
                            Cancel
                        </flux:button>

                        <flux:button
                            type="submit"
                            wire:loading.attr='disabled'
                        >
                            Update Budget
                        </flux:button>
                    </div>
                </div>
            </form>
        </x-popup>
    @endif

    {{-- Delete Modal --}}
    @if ($showDeleteModal == true)
        <x-popup close="closeDeleteModal">
            <form wire:submit='deleteBudget' class="space-y-6">
                <div class="flex flex-row justify-between items-center">
                    <flux:heading size="xl">
                        Delete {{ $selectedBudget->category->name }} budget
                    </flux:heading>

                    <flux:button wire:click='closeDeleteModal'>
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
                            wire:click='closeDeleteModal'
                        >
                            Cancel
                        </flux:button>

                        <flux:button
                            type="submit"
                            wire:loading.attr='disabled'
                        >
                            Delete Budget
                        </flux:button>
                    </div>
                </div>
            </form>
        </x-popup>
    @endif

    <div class="mt-6">
        {{ $this->budgets()->links() }}
    </div>
</div>
