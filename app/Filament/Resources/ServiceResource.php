<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service; // Corrected to Service model
use Filament\Forms\Form;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model; // For mutateFormData
use App\Models\User; // For creator/editor relationships

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    // Using a general Heroicon icon for the navigation
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Services';
    protected static ?string $navigationGroup = 'Site Content'; // A new group for general site content

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull()
                    ->autofocus(),

                // We keep this field to store the Font Awesome class for frontend use
                TextInput::make('icon_class')
                    ->label('Font Awesome Icon Class')
                    ->placeholder('e.g., fa-solid fa-boxes-packing, fa-solid fa-snowflake')
                    ->maxLength(255)
                    ->helperText('Find icons at fontawesome.com. Use "fa-solid fa-iconname".'),

                RichEditor::make('description')
                    ->label('Description')
                    ->placeholder('A detailed description of this service.')
                    ->toolbarButtons(['bold', 'italic', 'link', 'blockquote', 'strike', 'bulletList', 'orderedList'])
                    ->columnSpanFull(),

                TextInput::make('order')
                    ->label('Display Order')
                    ->numeric()
                    ->default(0)
                    ->rules(['min:0'])
                    ->helperText('Lower numbers appear first.'),

                Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true)
                    ->helperText('Deactivate to hide this service from the public website.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                // The 'icon_class' column is TEMPORARILY OMITTED FROM THE TABLE
                // to bypass the SvgNotFound error.

                TextColumn::make('description')
                    ->limit(70)
                    ->tooltip(fn (string $state): string => $state)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('order')
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label('Active'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('editor.name')
                    ->label('Last Updated By')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order', 'asc'); // Default sort by order column
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }

    // Auto-populate created_by and updated_by
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();
        return $data;
    }
}