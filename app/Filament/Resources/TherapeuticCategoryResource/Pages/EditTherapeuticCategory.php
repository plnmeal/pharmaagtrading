<?php

namespace App\Filament\Resources\TherapeuticCategoryResource\Pages;

use App\Filament\Resources\TherapeuticCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTherapeuticCategory extends EditRecord
{
    protected static string $resource = TherapeuticCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
