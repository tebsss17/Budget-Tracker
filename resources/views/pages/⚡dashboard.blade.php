<?php

use Livewire\Component;
use Flux\Flux;

new class extends Component
{
    // VARIRABLES
    public $step = 1;
    public $starting_balance = null;
    public $chartRange = '3_months';


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
        if($this->BudgetPercentage <= 50){
            return 'progress-success';
        }

        if($this->BudgetPercentage <= 75){
            return 'progress-info';
        }

        if($this->BudgetPercentage <= 90 ){
            return 'progress-warning';
        }

        return 'progress-error';
    }

    public function getBudgetProgress()
    {
        return Auth::user()->budget()
            ->with('category')
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->orderBy('created_at','desc')
            ->get();
    }

    public function getRecentTransaction()
    {
        return Auth::user()->transaction()
            ->with('category')
            ->latest('transaction_date')
            ->take(10)
            ->get();
    }

    public function monthlySpendingChart()
    {
        return match ($this->chartRange){
            '30_days' => $this->lastThirtyDaysChart(),
            '3_months' => $this->lastThreeMonthsChart(),
            '6_months' => $this->lastSixMonthsChart(),
            'this_year' => $this->thisYearChart(),
            'all' => $this->allTimeChart(),
            default => collect(),
        };
    }

    private function expenseQuery()
    {
        return Auth::user()->transaction()
            ->where('type', 'Expense');
    }

    public function updateChartRange()
    {
        $this->dispatch(
            'update-chart',

            data: $this->monthlySpendingChart()
        );
    }

    private function lastThirtyDaysChart()
    {
        return $this->expenseQuery()
            ->whereDate(
                'transaction_date', '>=', now()->subDays(29)
            )
            ->selectRaw('
                DATE(transaction_date) as label,
                SUM(amount) as total
            ')
            ->groupBy('label')
            ->orderBy('label')
            ->get();
    }

    private function lastThreeMonthsChart()
    {
        return $this->expenseQuery()
            ->whereDate(
                'transaction_date', '>=', now()->subMonths(2)->startOfMonth()
            )
            ->selectRaw('
                EXTRACT(YEAR FROM transaction_date) as year,
                EXTRACT(MONTH FROM transaction_date) as month,
                SUM(amount) as total
            ')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
    }

    private function lastSixMonthsChart()
    {
        return $this->expenseQuery()
            ->whereDate(
                'transaction_date', '>=', now()->subMonths(5)->startOfMonth()
            )
            ->selectRaw('
                EXTRACT(YEAR FROM transaction_date) as year,
                EXTRACT(MONTH FROM transaction_date) as month,
                SUM(amount) as total
            ')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
    }

    private function thisYearChart()
    {
        return $this->expenseQuery()
            ->whereYear(
                'transaction_date', now()->year
            )
            ->selectRaw('
                EXTRACT(MONTH FROM transaction_date) as month,
                SUM(amount) as total
            ')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    public function allTimeChart()
    {
        return $this->expenseQuery()
            ->selectRaw('
                EXTRACT(YEAR FROM transaction_date) as year,
                SUM(amount) as total
            ')
            ->groupBy('year')
            ->orderBy('year')
            ->get();
    }
};
?>

<div class="space-y-6">

    {{-- Fetching monthly net change --}}
    @php
        $netChange = Auth::user()->monthlyNetChange();
    @endphp

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

                    <p class="mt-2 text-sm {{ $netChange >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $netChange >= 0 ? '↑ +' : '↓ -' }}
                        ₱ {{ number_format($netChange, 2)  }}
                        this month
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

                    <x-mary-progress
                        class="mt-2 h-4.5 {{ $this->budgetColor() }}"
                        value="{{ $this->BudgetPercentage }}"
                        max="100"
                    />
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

                <flux:button variant="ghost" size="sm" href="{{ route('budgets') }}" wire:navigate>
                    View All
                </flux:button>
            </div>

            <div class="mt-6 space-y-6 max-h-[500px] overflow-y-auto">

                {{-- Food Budget --}}
                @forelse ($this->getBudgetProgress() as $budget)
                    <div>

                        <div class="flex items-start justify-between">

                            <div class="flex items-center gap-3">

                                <div class="rounded-xl p-2 {{ $budget->category->bg_color }}">
                                    <x-dynamic-component
                                        :component="'lucide-'.$budget->category->icon"
                                        class="size-5 {{ $budget->category->text_color }}"
                                    />
                                </div>

                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="font-semibold capitalize">
                                            {{ $budget->category->name }}
                                        </p>

                                        {{-- Condtional over budget --}}
                                        @if ($budget->isOverBudget())
                                            <div class="    flex items-center rounded-lg bg-rose-100 dark:bg-rose-900/40 px-2 py-0.5 text-xs font-semibold text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/50 animate-pulse">
                                                <x-lucide-triangle-alert class="size-4 shrink-0 mr-1" />
                                                Over Budget
                                            </div>
                                        @endif

                                    </div>

                                    <div>
                                        <p class="text-sm text-zinc-500">
                                            ₱{{ number_format($budget->spent(),2) }}
                                            /
                                            ₱{{ number_format($budget->amount_limit,2) }}
                                        </p>
                                    </div>

                                </div>
                            </div>

                            <div class="text-right">
                                <p class="font-semibold {{ $budget->textColor() }}">
                                    {{ round($budget->progress()) }}%
                                </p>
                            </div>

                        </div>

                        <x-mary-progress
                            class="mt-2 h-2 {{ $budget->progressColor() }}"
                            value="{{ $budget->progress() }}"
                            max="100"
                        />
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-10 text-center">
                        <div class="rounded-2xl bg-zinc-100 dark:bg-zinc-800 p-4">
                            <x-lucide-piggy-bank class="size-8 text-zinc-500" />
                        </div>

                        <flux:heading size="sm" class="mt-4">
                            No budgets this month
                        </flux:heading>

                        <flux:text class="mt-2 max-w-sm text-zinc-500">
                            Set a monthly budget to track your spending progress and avoid overspending.
                        </flux:text>

                        <flux:button
                            icon="plus-circle"
                            class="mt-6"
                            href="{{ route('budgets') }}"
                        >
                            Set Budget
                        </flux:button>
                    </div>

                @endforelse
            </div>
        </flux:card>

        {{-- Recent Transactions --}}
        <flux:card class="p-6">

            <div class="flex items-center justify-between">
                <flux:heading size="lg">
                    Recent Transactions
                </flux:heading>

                <flux:button variant="ghost" size="sm" href="{{ route('transactions') }}">
                    View All
                </flux:button>
            </div>

            <div class="mt-6 space-y-6 max-h-[500px] overflow-y-auto">
                @forelse ($this->getRecentTransaction() as $recentTransaction )
                    <div class="flex items-center justify-between">

                        <div class="flex gap-3 items-center">

                            <div class="rounded-xl p-2 {{ $recentTransaction->category->bg_color }}">
                                <x-dynamic-component
                                    :component="'lucide-'.$recentTransaction->category->icon"
                                    class="size-5 {{ $recentTransaction->category->text_color }}"
                                />
                            </div>

                            <div>
                                <p class="font-semibold capitalize">{{ $recentTransaction->category->name }}</p>
                                <p class="text-sm text-zinc-500">{{ Str::limit($recentTransaction->description ?: 'No Description', 20) }}</p>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            @if ($recentTransaction->type == 'Income')
                                <p class="font-semibold text-emerald-500">
                                    +₱ {{ number_format($recentTransaction->amount, 2) }}
                                </p>
                            @else
                                <p class="font-semibold text-rose-500">
                                    -₱ {{ number_format($recentTransaction->amount, 2) }}
                                </p>
                            @endif

                            <div>
                                <p class="text-sm text-zinc-500">
                                    {{ date('F j, Y', strtotime($recentTransaction->transaction_date)) }}
                                </p>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-10 text-center">
                        <div class="rounded-2xl bg-zinc-100 dark:bg-zinc-800 p-4">
                            <x-lucide-arrow-left-right class="size-8 text-zinc-500" />
                        </div>

                        <flux:heading size="sm" class="mt-4">
                            No transaction found
                        </flux:heading>

                        <flux:text class="mt-2 max-w-sm text-zinc-500">
                            Add your first income or expense to start tracking your finances.
                        </flux:text>

                        <flux:button
                            icon="plus-circle"
                            class="mt-6"
                            href="{{ route('transactions') }}"
                        >
                            Create Transaction
                        </flux:button>
                    </div>

                @endforelse
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
                {{-- Make this monthly for simplicity--}}
                <flux:select class="w-full sm:w-44" wire:model='chartRange' wire:change='updateChartRange'>
                    <flux:select.option value="all">All</flux:select.option>
                    <flux:select.option value="30_days">30 Days</flux:select.option>
                    <flux:select.option value="3_months">3 Months</flux:select.option>
                    <flux:select.option value="6_months">6 Months</flux:select.option>
                    <flux:select.option value="this_year">This Year</flux:select.option>

                </flux:select>

            </div>

            <div class="mt-8 h-80 rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700 flex items-center justify-center">
                <canvas
                    id="monthlySpendingChart"
                    wire:ignore
                ></canvas>
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

@script
<script>
        let spendingChart;

// Helper function para gawing pangalan ng buwan ang buwan na numero (e.g., 1 -> Jan)
const getMonthName = (monthNum) => {
    if (!monthNum) return '';
    const date = new Date();
    date.setMonth(monthNum - 1);
    return date.toLocaleString('default', { month: 'short' });
};

const renderChart = (data) => {
    const ctx = document.getElementById('monthlySpendingChart');
    if (!ctx) return;

    // Gumawa ng labels base sa kung anong data structure ang bumalik
    const labels = data.map(item => {
        if (item.label) return item.label; // Para sa 30 Days query
        if (item.month && item.year) return `${getMonthName(item.month)} ${item.year}`; // Para sa 3/6 Months
        if (item.month) return getMonthName(item.month); // Para sa This Year
        if (item.year) return item.year; // Para sa All Time
        return '';
    });

    const chartData = data.map(item => item.total);

    if (!spendingChart) {
        spendingChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Monthly Spending',
                        data: chartData,
                        borderWidth: 3,
                        tension: .4,
                        fill: true,
                        borderColor: '#10b981', // Emerald-500 para bumagay sa theme mo
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    } else {
        spendingChart.data.labels = labels;
        spendingChart.data.datasets[0].data = chartData;
        spendingChart.update();
    }
};

// I-render ang default chart sa unang load
renderChart(@js($this->monthlySpendingChart()));

// Makinig sa update-chart event mula sa Livewire
Livewire.on('update-chart', ({ data }) => {
    renderChart(data);
});
    </script>
@endscript
