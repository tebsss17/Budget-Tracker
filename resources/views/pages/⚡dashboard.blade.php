<?php

use Livewire\Component;
use Flux\Flux;

new class extends Component
{
    // VARIRABLES
    public $step = 1;
    public $starting_balance = null;

    public function mount()
    {
        if(Auth::user()->starting_balance === null){
            $this->step = 1;
        }else{
            $this->step = 0;
        }
    }

    public function nextStep()
    {
        $this->step = 2;

    }

    public function previousStep()
    {
        $this->step = 1;
    }

    public function saveBalance()
    {
        $validated = $this->validate([
            'starting_balance' => 'required|numeric|min:20'
        ],
        [
            'starting_balance.required' => 'Please enter your starting balance.',
            'starting_balance.numeric' => 'Starting balance must be a valid number.',
            'starting_balance.min' => 'Starting balance must be at least ₱20.'
        ]);

        Auth::user()->update($validated);

        Flux::toast(variant: 'success', text: 'Welcome to Budget Buddy! Your wallet has been set up successfully.');

        $this->step = 0;
    }


    // QUERY FUNCTIONS
    public function getIncomeThisMonthProperty()
    {
        return Auth::user()->transaction()
            ->where('type', 'Income')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');
    }

    public function getExpenseThisMonthProperty()
    {
        return Auth::user()->transaction()
            ->where('type', 'Expense')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');
    }

    public function getMonthlyBudgetProperty()
    {
        return Auth::user()->budget()
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->sum('amount_limit');
    }

    public function getBudgetPercentageProperty()
    {
        if($this->MonthlyBudget == 0) {
            return 0;
        }

        return min(
            ($this->ExpenseThisMonth / $this->MonthlyBudget) * 100, 100
        );
    }

    public function budgetColor()
    {
        if($this->BudgetPercentage <= 30){
            return 'emerald';
        }

        if($this->BudgetPercentage <= 50){
            return 'line';
        }

        if($this->BudgetPercentage <= 75 ){
            return 'amber';
        }

        return 'red';
    }

    public function getBudgetProgress()
    {
        return Auth::user()->budget()
            ->with('category')
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->get();
    }
};
?>

<div class="space-y-6">

    {{-- Breadcrumbs --}}
    <div>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item>
                Home
            </flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        {{-- Greeting --}}
        <div>
            <flux:heading size="xl" class="font-bold">
                Good {{ now()->format('A') == 'AM' ? 'Morning' : (now()->hour < 18 ? 'Afternoon' : 'Evening') }},
                {{ auth()->user()->name }} 👋
            </flux:heading>

            <flux:text class="mt-1 text-zinc-500">
                Here's an overview of your finances today.
            </flux:text>
        </div>

        {{-- Current Date --}}
        <div>
            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 text-right">
                {{ now()->format('F d, Y') }}
            </p>

            <p class="text-xs text-zinc-500 text-right">
                {{ now()->format('l') }}
            </p>
        </div>
    </div>

    {{-- Main Section --}}
    <div class="mt-10 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- Total Balance --}}
        <flux:card class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-500">Available Balance</p>

                    <h2 class="mt-2 text-3xl font-bold tracking-tight">
                        ₱ {{ number_format(Auth::user()->balance(), 2) }}
                    </h2>

                    <p class="mt-2 text-sm text-emerald-600">
                        ↑ +₱2,350 this month
                    </p>
                </div>

                <div class="rounded-xl bg-emerald-100 p-3 dark:bg-emerald-900/30">
                    <x-lucide-wallet class="h-6 w-6 text-emerald-600" />
                </div>
            </div>
        </flux:card>

        {{-- Income --}}
        <flux:card class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-500">Income</p>

                    <h2 class="mt-2 text-3xl font-bold">
                        ₱ {{ number_format($this->IncomeThisMonth, 2) }}
                    </h2>

                    <p class="mt-2 text-sm text-zinc-500">
                        This month
                    </p>
                </div>

                <div class="rounded-xl bg-green-100 p-3 dark:bg-green-900/30">
                    <x-lucide-trending-up class="h-6 w-6 text-green-600" />
                </div>
            </div>
        </flux:card>

        {{-- Expenses --}}
        <flux:card class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-500">Expenses</p>

                    <h2 class="mt-2 text-3xl font-bold">
                        ₱ {{ number_format($this->ExpenseThisMonth, 2) }}
                    </h2>

                    <p class="mt-2 text-sm text-zinc-500">
                        This month
                    </p>
                </div>

                <div class="rounded-xl bg-red-100 p-3 dark:bg-red-900/30">
                    <x-lucide-trending-down class="h-6 w-6 text-red-600" />
                </div>
            </div>
        </flux:card>

        {{-- Budget Used --}}
        <flux:card class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-500">Budget Used</p>

                    <h2 class="mt-2 text-3xl font-bold">
                        {{ round($this->BudgetPercentage) }}%
                    </h2>

                    <flux:progress value="{{ $this->BudgetPercentage }}" color="{{ $this->budgetColor() }}"  class="h-3 mt-3" max="100"></flux:progress>
                </div>

                <div class="rounded-xl bg-blue-100 p-3 dark:bg-blue-900/30">
                    <x-lucide-target class="h-6 w-6 text-blue-600" />
                </div>
            </div>
        </flux:card>
    </div>

    {{-- Progress and Transaction Cards --}}
    <div class="mt-10 grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- Budget Progress --}}
        <flux:card class="xl:col-span-2 p-6">

            <div class="flex items-center justify-between">
                <flux:heading size="lg">
                    Budget Progress This Month
                </flux:heading>

                <flux:button variant="ghost" size="sm">
                    View All
                </flux:button>
            </div>

            <div class="mt-6 space-y-6">

                {{-- Food --}}
                @foreach ($this->getBudgetProgress() as $budget )
                        {{ $budget->category }}
                @endforeach
                <div>
                    <div class="mb-2 flex items-center justify-between">

                        <div class="flex items-center gap-3">
                            <div class="rounded-lg bg-orange-100 p-2 dark:bg-orange-900/30">
                                <x-lucide-utensils class="h-4 w-4 text-orange-600"/>
                            </div>

                            <div>
                                <p class="font-medium">Food</p>
                                <p class="text-sm text-zinc-500">
                                    ₱3,500 / ₱5,000
                                </p>
                            </div>
                        </div>

                        <span class="font-semibold text-emerald-600">
                            70%
                        </span>

                    </div>

                    <div class="h-2 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700">
                        <div class="h-full w-[70%] rounded-full bg-emerald-500"></div>
                    </div>
                </div>

                {{-- Transportation --}}
                <div>
                    <div class="mb-2 flex items-center justify-between">

                        <div class="flex items-center gap-3">
                            <div class="rounded-lg bg-blue-100 p-2 dark:bg-blue-900/30">
                                <x-lucide-car class="h-4 w-4 text-blue-600"/>
                            </div>

                            <div>
                                <p class="font-medium">Transportation</p>
                                <p class="text-sm text-zinc-500">
                                    ₱800 / ₱2,000
                                </p>
                            </div>
                        </div>

                        <span class="font-semibold text-amber-500">
                            40%
                        </span>

                    </div>

                    <div class="h-2 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700">
                        <div class="h-full w-[40%] rounded-full bg-amber-500"></div>
                    </div>
                </div>

                {{-- Bills --}}
                <div>
                    <div class="mb-2 flex items-center justify-between">

                        <div class="flex items-center gap-3">
                            <div class="rounded-lg bg-purple-100 p-2 dark:bg-purple-900/30">
                                <x-lucide-receipt class="h-4 w-4 text-purple-600"/>
                            </div>

                            <div>
                                <p class="font-medium">Bills</p>
                                <p class="text-sm text-zinc-500">
                                    ₱5,000 / ₱5,000
                                </p>
                            </div>
                        </div>

                        <span class="font-semibold text-red-500">
                            100%
                        </span>

                    </div>

                    <div class="h-2 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700">
                        <div class="h-full w-full rounded-full bg-red-500"></div>
                    </div>
                </div>
            </div>
        </flux:card>

        {{-- Recent Transactions --}}
        <flux:card class="p-6">

            <div class="flex items-center justify-between">
                <flux:heading size="lg">
                    Recent Transactions
                </flux:heading>

                <flux:button variant="ghost" size="sm">
                    View All
                </flux:button>
            </div>

            <div class="mt-6 space-y-5">

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">

                        <div class="rounded-lg bg-emerald-100 p-2 dark:bg-emerald-900/30">
                            <x-lucide-briefcase class="h-4 w-4 text-emerald-600"/>
                        </div>

                        <div>
                            <p class="font-medium">Salary</p>
                            <p class="text-xs text-zinc-500">
                                Today
                            </p>
                        </div>
                    </div>

                    <span class="font-semibold text-emerald-600">
                        +₱25,000
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">

                        <div class="rounded-lg bg-red-100 p-2 dark:bg-red-900/30">
                            <x-lucide-utensils class="h-4 w-4 text-red-600"/>
                        </div>

                        <div>
                            <p class="font-medium">Jollibee</p>
                            <p class="text-xs text-zinc-500">
                                Food
                            </p>
                        </div>
                    </div>

                    <span class="font-semibold text-red-500">
                        -₱250
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">

                        <div class="rounded-lg bg-red-100 p-2 dark:bg-red-900/30">
                            <x-lucide-fuel class="h-4 w-4 text-red-600"/>
                        </div>

                        <div>
                            <p class="font-medium">Gasoline</p>
                            <p class="text-xs text-zinc-500">
                                Transportation
                            </p>
                        </div>
                    </div>

                    <span class="font-semibold text-red-500">
                        -₱500
                    </span>
                </div>
            </div>
        </flux:card>
    </div>

    {{-- Monthy Spending Analytics --}}
    <div class="mt-10">

        <flux:card class="p-6">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <flux:heading size="lg">
                        Monthly Spending
                    </flux:heading>

                    <flux:text class="text-zinc-500">
                        Track your spending trend over the past months.
                    </flux:text>
                </div>

                <flux:select class="w-full sm:w-44">
                    <option>This Year</option>
                    <option>Last 6 Months</option>
                    <option>Last 30 Days</option>
                </flux:select>

            </div>

            <div class="mt-8 h-80 rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700 flex items-center justify-center">

                <div class="text-center">

                    <x-lucide-chart-column class="mx-auto h-14 w-14 text-zinc-400"/>

                    <p class="mt-3 font-medium">
                        Monthly Spending Chart
                    </p>

                    <p class="text-sm text-zinc-500">
                        Chart.js will be displayed here.
                    </p>

                </div>

            </div>

        </flux:card>

    </div>


    {{-- Starting Balance Modal --}}
    @if ($step == 1)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">

            <div class="w-full max-w-lg rounded-2xl bg-white dark:bg-zinc-800 shadow-2xl p-6 sm:p-8">

                {{-- Logo --}}
                <div class="flex justify-center">
                    <div class="flex h-20 w-20 sm:h-24 sm:w-24 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/40">
                        <x-lucide-wallet class="h-10 w-10 sm:h-12 sm:w-12 text-emerald-600"/>
                    </div>
                </div>

                {{-- Heading --}}
                <div class="mt-6 text-center">
                    <flux:heading size="xl">
                        Welcome, {{ auth()->user()->name }}! 👋
                    </flux:heading>

                    <flux:text class="mt-3 text-zinc-500 leading-relaxed">
                        Welcome to
                        <span class="font-semibold text-emerald-600">
                            Budget Buddy
                        </span>.

                        Your personal companion for smarter budgeting and expense tracking.
                        Let's get everything ready before you start managing your finances.
                    </flux:text>
                </div>

                {{-- Features --}}
                <div class="mt-8 space-y-4">

                    <div class="flex items-start gap-3">
                        <x-lucide-check-circle-2 class="mt-0.5 h-5 w-5 shrink-0 text-emerald-500"/>
                        <span class="text-sm sm:text-base">
                            Track every income and expense.
                        </span>
                    </div>

                    <div class="flex items-start gap-3">
                        <x-lucide-check-circle-2 class="mt-0.5 h-5 w-5 shrink-0 text-emerald-500"/>
                        <span class="text-sm sm:text-base">
                            Stay on top of your monthly budgets.
                        </span>
                    </div>

                    <div class="flex items-start gap-3">
                        <x-lucide-check-circle-2 class="mt-0.5 h-5 w-5 shrink-0 text-emerald-500"/>
                        <span class="text-sm sm:text-base">
                            Always know your remaining balance.
                        </span>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="mt-10 flex flex-col gap-4 border-t pt-6 sm:flex-row sm:items-center sm:justify-between">

                    <span class="text-center text-sm text-zinc-500 sm:text-left">
                        Step 1 of 2
                    </span>

                    <flux:button
                        wire:click="nextStep"
                        variant="primary"
                        color="emerald"
                        icon:trailing="arrow-right"
                        class="w-full sm:w-auto"
                    >
                        Next
                    </flux:button>

                </div>

            </div>

        </div>
    @endif

    {{-- Step 2 --}}
    @if ($step == 2)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">

            <div class="w-full max-w-lg rounded-2xl bg-white dark:bg-zinc-800 shadow-2xl p-6 sm:p-8">

                <form wire:submit='saveBalance'>
                    {{-- Logo --}}
                    <div class="flex justify-center">
                        <div class="flex h-20 w-20 sm:h-24 sm:w-24 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/40">
                            <x-lucide-wallet class="h-10 w-10 sm:h-12 sm:w-12 text-emerald-600"/>
                        </div>
                    </div>

                    {{-- Heading --}}
                    <div class="mt-6 text-center">
                        <flux:heading size="xl">
                            Wallet Setup
                        </flux:heading>

                        <flux:text class="mt-3 text-zinc-500 leading-relaxed">
                            Let's start by adding your current available balance. <br>
                            This helps us accurately calculate your remaining balance and spending.
                        </flux:text>
                    </div>

                    {{-- Money Input --}}
                    <div class="mt-8">
                        <x-mary-input
                            wire:model="starting_balance"
                            prefix="₱"
                            step="0.01"
                            placeholder="0.00"
                            type="number"
                        />

                        <div class="mt-3 flex items-start gap-2 rounded-lg bg-blue-50 p-3 text-sm text-blue-700 dark:bg-blue-950/40 dark:text-blue-300">
                            <x-lucide-info class="mt-0.5 h-4 w-4 shrink-0" />

                            <span>
                                This is a one-time setup. Your starting balance will be used to calculate your remaining balance and cannot be changed later.
                            </span>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="mt-10 flex flex-col gap-4 border-t pt-6 sm:flex-row sm:items-center sm:justify-between">

                        <flux:button
                            type="button"
                            wire:click="previousStep"
                            variant="primary"
                            color="emerald"
                            icon="arrow-left"
                            class="w-full sm:w-auto duration-300"
                        >
                            Back
                        </flux:button>

                        <span class="text-center text-sm text-zinc-500 sm:text-left">
                            Step 2 of 2
                        </span>

                        <flux:button
                            type="submit"
                            variant="primary"
                            color="emerald"
                            icon="check-circle"
                            wire:loading.attr='disabled'
                            class="w-full sm:w-auto"
                        >
                            Save & Continue
                        </flux:button>

                    </div>
            </form>
        </div>
    @endif



</div>
