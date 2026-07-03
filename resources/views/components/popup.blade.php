@props(['close'])

    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4" wire:click='{{ $close }}'>
        <div class="w-full max-w-lg rounded-2xl bg-white dark:bg-zinc-800 shadow-2xl p-6 sm:p-8" wire:click.stop>
            {{ $slot }}
        </div>
    </div>
