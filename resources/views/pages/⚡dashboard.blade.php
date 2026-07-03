<?php

use Livewire\Component;

new class extends Component
{
    // VARIRABLES
    public $showOnboarding = false;
    public $step = 1;
    public $starting_balance = 0;

    public function mount()
    {
        if(Auth::user()->starting_balance === null){
            $this->showOnboarding = true;
        }
    }

    public function nextStep()
    {
        $this->showOnboarding = false;
        $this->step++;

    }

    public function previousStep()
    {
        $this->step--;
        $this->showOnboarding = true;
    }

    public function saveBalace()
    {
        $this->validate([
            'starting_balance' => 'required|min:20'
        ])
    }
};
?>

<div>
    {{-- Starting Balance Modal --}}
    @if ($showOnboarding)
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
                        money
                        step="0.01"
                        placeholder="0.00"
                    />
                    @error('starting_balance')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Footer --}}
                <div class="mt-10 flex flex-col gap-4 border-t pt-6 sm:flex-row sm:items-center sm:justify-between">

                    <flux:button
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
                        wire:click="nextStep"
                        variant="primary"
                        color="emerald"
                        icon:trailing="arrow-right"
                        class="w-full sm:w-auto"
                    >
                        Save
                    </flux:button>

                </div>



        </div>
    @endif


</div>
