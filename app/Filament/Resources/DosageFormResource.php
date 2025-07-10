<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DosageFormResource\Pages;
use App\Filament\Resources\DosageFormResource\RelationManagers;
use App\Models\DosageForm;
use Filament\Forms\Form;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section; // Import Section
use Filament\Forms\Components\Tabs; // IMPORT THIS
use Filament\Forms\Components\Tabs\Tab; // IMPORT THIS
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Filament\Forms\Set;
use Filament\Forms\Get;

class DosageFormResource extends Resource
{
    protected static ?string $model = DosageForm::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube'; // Using cube as it's a safe icon.
    protected static ?string $navigationLabel = 'Dosage Forms';
    protected static ?string $navigationGroup = 'Product Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Main tab structure for multilingual content
                Tabs::make('Dosage Form Content')
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
                                    ->unique(ignoreRecord: true, table: DosageForm::class, column: 'name_es') // Unique in Spanish names
                                    ->helperText('Spanish name.'),

                                RichEditor::make('description_es')
                                    ->label('Description (Spanish)')
                                    ->toolbarButtons(['bold', 'italic', 'link', 'blockquote', 'strike', 'bulletList', 'orderedList']),
                            ])->columns(1), // Use 1 column for content in tabs
                    ])->columnSpanFull(), // Make tabs span full width

                // Separate section for common dosage form details (not language specific)
                Section::make('Dosage Form Details')
                    ->schema([
                        TextInput::make('icon_class')
                            ->label('Font Awesome Icon Class')
                            ->placeholder('e.g., fa-solid fa-syringe, fa-pills')
                            ->maxLength(255)
                            ->helperText('Find icons at fontawesome.com. Use "fa-solid fa-iconname".'),

                        TextInput::make('order')
                            ->label('Display Order')
                            ->numeric()
                            ->default(0)
                            ->rules(['min:0'])
                            ->helperText('Lower numbers appear first.'),

                        Toggle::make('is_active')
                            ->label('Is Active')
                            ->default(true)
                            ->helperText('Deactivate to hide this dosage form from the website.'),

                        Toggle::make('is_featured')
                            ->label('Show on Homepage (Featured)')
                            ->default(false)
                            ->helperText('Enable to display this dosage form in the "A Comprehensive Product Portfolio" section on the homepage.'),
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
                TextColumn::make('icon_class') // We are temporarily showing icon class as text for debugging
                    ->label('Icon Class')
                    ->tooltip(fn (string $state): string => $state)
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('is_active')
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
            'index' => Pages\ListDosageForms::route('/'),
            'create' => Pages\CreateDosageForm::route('/create'),
            'edit' => Pages\EditDosageForm::route('/{record}/edit'),
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