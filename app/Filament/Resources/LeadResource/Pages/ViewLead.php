<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord; // IMPORTANT: Use ViewRecord

class ViewLead extends ViewRecord
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(), // Allow editing from the view page
        ];
    }
}