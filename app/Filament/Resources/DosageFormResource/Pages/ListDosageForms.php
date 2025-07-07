<?php

namespace App\Filament\Resources\DosageFormResource\Pages;

use App\Filament\Resources\DosageFormResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDosageForms extends ListRecords
{
    protected static string $resource = DosageFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
