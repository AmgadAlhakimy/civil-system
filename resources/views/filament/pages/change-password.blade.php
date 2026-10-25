<x-filament-panels::page>
    <div class="mx-auto w-full max-w-md">
        <form wire:submit="save" class="flex flex-col gap-y-6">

            {{ $this->form }}

            <x-filament::button
                type="submit"
                class="w-full"
            >
                حفظ كلمة المرور الجديدة
            </x-filament::button>
        </form>
    </div>
</x-filament-panels::page>
