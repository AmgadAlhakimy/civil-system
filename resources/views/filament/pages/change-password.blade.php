<x-filament-panels::page>
    <div class="mx-auto w-full max-w-md">
        <form wire:submit="save" class="flex flex-col gap-y-6">

            {{ $this->form }}

            <x-filament::button
                type="submit"
                class="w-full"
                wire:loading.attr="disabled"
                wire:target="save"
            >
                <span wire:loading.remove wire:target="save">
                    حفظ كلمة المرور الجديدة
                </span>

                <span wire:loading wire:target="save">
                    جارٍ حفظ كلمة المرور...
                </span>
            </x-filament::button>

        </form>
    </div>
</x-filament-panels::page>
