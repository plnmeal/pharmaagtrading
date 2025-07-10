<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use App\Models\Setting; // Import Setting model
use Filament\Resources\Pages\ListRecords;
use Illuminate\Http\RedirectResponse; // For redirect

class ListSettings extends ListRecords
{
    protected static string $resource = SettingResource::class;

    public function mount(): void
    {
        parent::mount();

        // Find the settings record with ID 1
        $settings = Setting::find(1);

        // If it doesn't exist, create it. This ensures it's always there.
        if (!$settings) {
            $settings = Setting::create([
                'id' => 1, // Explicitly set ID to 1
                'site_name' => 'pharmaagtrading',
                // Add other non-nullable default values here if your migration has them
                // This should ideally match the defaults in Settings.php's mount or migration
            ]);
        }

        // Redirect directly to the edit page of the single record (ID 1)
        // This is the key to making the Resource behave like a single-item editor.
        $this->redirect(SettingResource::getUrl('edit', ['record' => $settings->id]));
    }
}