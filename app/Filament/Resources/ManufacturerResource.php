<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManufacturerResource\Pages;
use App\Filament\Resources\ManufacturerResource\RelationManagers;
use App\Models\Manufacturer; // Corrected to Manufacturer model
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\RichEditor; // Import RichEditor
use Filament\Forms\Components\TextInput; // Import TextInput
use Filament\Forms\Components\FileUpload; // Import FileUpload
use Filament\Forms\Components\Toggle; // Import Toggle
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn; // Import ImageColumn
use Filament\Tables\Columns\TextColumn; // Import TextColumn
use Filament\Tables\Columns\ToggleColumn; // Import ToggleColumn
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model; // Import Model (for mutateFormData)

class ManufacturerResource extends Resource
{
    protected static ?string $model = Manufacturer::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2'; // A good icon for manufacturers
    protected static ?string $navigationLabel = 'Manufacturers'; // The label in the sidebar
    protected static ?string $navigationGroup = 'Product Management'; // Group in sidebar

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true) // Ensure names are unique (except for the current record when editing)
                    ->columnSpanFull() // Take full width in form
                    ->autofocus(), // Automatically focus this field when opening the form

                RichEditor::make('description')
                    ->label('Description')
                    ->placeholder('Provide a brief description of the manufacturer.')
                    ->toolbarButtons(['bold', 'italic', 'link', 'blockquote', 'strike', 'bulletList', 'orderedList'])
                    ->columnSpanFull(),

                FileUpload::make('logo_path')
                    ->label('Manufacturer Logo')
                    ->image()
                    ->directory('manufacturers-logos') // Store images in storage/app/public/manufacturers-logos
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('website_url')
                    ->label('Website URL')
                    ->url() // Adds URL validation
                    ->maxLength(255)
                    ->columnSpanFull(),

                TextInput::make('order')
                    ->label('Display Order')
                    ->numeric() // Only allows numbers
                    ->default(0)
                    ->rules(['min:0'])
                    ->helperText('Lower numbers appear first (e.g., 0, 1, 2...).'),

                Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true)
                    ->helperText('Deactivate to hide this manufacturer from the public website.'),

                Toggle::make('is_featured')
                    ->label('Show on Homepage (Featured)')
                    ->default(false)
                    ->helperText('Enable to display this manufacturer in the "Trusted by Healthcare Leaders" section on the homepage.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->height(50)
                    ->circular() // Makes image circular if desired
                    ->defaultImageUrl(url('/images/default-logo.png')), // Optional: A default logo if none is set
                TextColumn::make('description')
                    ->limit(70)
                    ->tooltip(fn (string $state): string => $state), // Show full description on hover
                TextColumn::make('website_url')
                    ->url(fn (Manufacturer $record) => $record->website_url) // Corrected record type hint
                    ->openUrlInNewTab()
                    ->limit(30)
                    ->copyable()
                    ->icon('heroicon-o-link'),
                TextColumn::make('order')
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label('Active'),
                ToggleColumn::make('is_featured')
                    ->label('Featured'),
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
            'index' => Pages\ListManufacturers::route('/'),
            'create' => Pages\CreateManufacturer::route('/create'),
            'edit' => Pages\EditManufacturer::route('/{record}/edit'),
        ];
    }

    // Add this to auto-populate created_by and updated_by
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