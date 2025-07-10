<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManufacturerResource\Pages;
use App\Filament\Resources\ManufacturerResource\RelationManagers;
use App\Models\Manufacturer;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Tabs; // IMPORT THIS
use Filament\Forms\Components\Tabs\Tab; // IMPORT THIS
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Filament\Notifications\Notification; // For notifications
use Filament\Forms\Set; // For afterStateUpdated if needed
use Filament\Forms\Get; // For conditionals if needed

class ManufacturerResource extends Resource
{
    protected static ?string $model = Manufacturer::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Manufacturers';
    protected static ?string $navigationGroup = 'Product Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Main tab structure for multilingual content
                Tabs::make('Manufacturer Content')
                    ->tabs([
                        Tab::make('English')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true) // Unique in English names
                                    ->label('Name (English)'),

                                RichEditor::make('description')
                                    ->label('Description (English)')
                                    ->toolbarButtons(['bold', 'italic', 'link', 'blockquote', 'strike', 'bulletList', 'orderedList']),
                            ])->columns(1), // Use 1 column for content in tabs for readability

                        Tab::make('Spanish')
                            ->schema([
                                TextInput::make('name_es')
                                    ->label('Name (Spanish)')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true, table: Manufacturer::class, column: 'name_es') // Unique in Spanish names
                                    ->helperText('Spanish name.'),

                                RichEditor::make('description_es')
                                    ->label('Description (Spanish)')
                                    ->toolbarButtons(['bold', 'italic', 'link', 'blockquote', 'strike', 'bulletList', 'orderedList']),
                            ])->columns(1), // Use 1 column for content in tabs
                    ])->columnSpanFull(), // Make tabs span full width

                // Separate section for common manufacturer details (not language specific)
                Section::make('Manufacturer Details')
                    ->schema([
                        FileUpload::make('logo_path')
                            ->label('Manufacturer Logo')
                            ->image()
                            ->directory('manufacturers-logos')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('website_url')
                            ->label('Website URL')
                            ->url()
                            ->maxLength(255),

                        TextInput::make('order')
                            ->label('Display Order')
                            ->numeric()
                            ->default(0)
                            ->rules(['min:0'])
                            ->helperText('Lower numbers appear first.'),

                        Toggle::make('is_active')
                            ->label('Is Active')
                            ->default(true)
                            ->helperText('Deactivate to hide this manufacturer from the public website.'),

                        Toggle::make('is_featured')
                            ->label('Show on Homepage (Featured)')
                            ->default(false)
                            ->helperText('Enable to display this manufacturer in the "Trusted by Healthcare Leaders" section on the homepage.'),
                    ])->columns(2), // Layout this section in 2 columns
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name (EN)')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('name_es') // Display Spanish name in table (toggleable)
                    ->label('Name (ES)')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->height(50)
                    ->circular()
                    ->defaultImageUrl(url('/images/default-logo.png')),
                TextColumn::make('website_url')
                    ->url(fn ($record) => $record->website_url)
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
                Tables\Filters\SelectFilter::make('is_active') // Filtering by active status
                    ->label('Status')
                    ->options([
                        true => 'Active',
                        false => 'Inactive',
                    ]),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured Status')
                    ->boolean(),
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
            ->defaultSort('order', 'asc');
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