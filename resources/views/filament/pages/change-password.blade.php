<x-filament-panels::page>
    <div class="mx-auto w-full max-w-md">
        <form wire:submit="save">
            {{ $this->form }}

            <button
                type="submit"
                class="sr-only"
                tabindex="-1"
                aria-hidden="true"
            ></button>
        </form>
    </div>
</x-filament-panels::page>
