<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-2">
            <x-filament::button
                type="submit"
                icon="heroicon-o-check"
                size="lg"
            >
                Tüm Ayarları Kaydet
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>

