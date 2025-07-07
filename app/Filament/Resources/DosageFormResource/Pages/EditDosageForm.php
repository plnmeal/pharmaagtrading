<?php

namespace App\Filament\Resources\DosageFormResource\Pages;

use App\Filament\Resources\DosageFormResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDosageForm extends EditRecord
{
    protected static string $resource = DosageFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
