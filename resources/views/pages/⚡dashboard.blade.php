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
            'starting_balance.min' => 'Starging balance must be at least ₱20.'
        ]);

        Auth::user()->update($validated);

        Flux::toast(variant: 'success', text: 'Welcome to Budget Buddy! Your wallet has been set up successfully.');

        $this->step = 0;
    }
};
?>

<div>
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
                            class="w-full sm:w-auto"
                        >
                            Save & Continue
                        </flux:button>

                    </div>
            </form>
        </div>
    @endif


</div>
