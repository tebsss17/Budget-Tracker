@props(['close'])

    <div class="fixed bg-black/50 inset-0 z-50 flex items-center justify-center" wire:click='{{ $close }}'>
        <div class="bg-white rounded-xl p-6 max-w-md md:w-full dark:bg-zinc-700" wire:click.stop>
            {{ $slot }}
        </div>
    </div>
