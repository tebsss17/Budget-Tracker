<?php

use Livewire\Component;

new class extends Component
{
    // VARIRABLES
    public $startingBalanceModal = false;

    public function mount()
    {
        if(Auth::user()->starting_balance === null){
            $this->startingBalanceModal = true;
        }
    }
};
?>

<div>
    {{-- Starting Balance Modal --}}
    @if ($startingBalanceModal == true)
        <div class="fixed bg-black/50 inset-0 z-50 flex justify-center items-center">
            <div class="bg-white rounded-xl p-6 max-w-md md:w-full dark:bg-zinc-700">
                <div class="flex flex-row gap-2 ">
                    <x-lucide-car/>
                    <flux:heading size="xl">Welcome! {{ auth()->user()->name }}</flux:heading>
                </div>
            </div>
        </div>
    @endif
</div>
