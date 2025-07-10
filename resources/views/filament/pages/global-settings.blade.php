{{-- resources/views/filament/pages/global-settings.blade.php --}}
<x-filament-panels::page>
    <x-filament-forms::form wire:submit.prevent="save">
        {{ $this->form }}

        <div class="filament-form-actions flex items-center space-x-2 rtl:space-x-reverse mt-6">
            @foreach ($this->getFormActions() as $action)
                {{ $action }}
            @endforeach
        </div>
    </x-filament-forms::form>
</x-filament-panels::page>
