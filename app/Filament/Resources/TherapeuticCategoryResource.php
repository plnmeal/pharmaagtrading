<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TherapeuticCategoryResource\Pages;
use App\Filament\Resources\TherapeuticCategoryResource\RelationManagers;
use App\Models\TherapeuticCategory;
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
use Illuminate\Support\Str;
use Filament\Forms\Set;
use Filament\Forms\Get;

class TherapeuticCategoryResource extends Resource
{
    protected static ?string $model = TherapeuticCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $navigationLabel = 'Therapeutic Categories';
    protected static ?string $navigationGroup = 'Product Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Main tab structure for multilingual content
                Tabs::make('Category Content')
                    ->tabs([
                        Tab::make('English')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true) // Unique in English names
                                    ->live(onBlur: true) // Update slug as name is typed (on blur)
                                    ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
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
                                    ->unique(ignoreRecord: true, table: TherapeuticCategory::class, column: 'name_es') // Unique in Spanish names
                                    ->helperText('Spanish name.'),

                                RichEditor::make('description_es')
                                    ->label('Description (Spanish)')
                                    ->toolbarButtons(['bold', 'italic', 'link', 'blockquote', 'strike', 'bulletList', 'orderedList']),
                            ])->columns(1), // Use 1 column for content in tabs
                    ])->columnSpanFull(), // Make tabs span full width

                // Separate section for common category details (not language specific)
                Section::make('Category Details')
                    ->schema([
                        TextInput::make('slug') // Slug should not be in tabs, as it's common
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('A unique URL-friendly identifier. Auto-generated from name (English).'),

                        TextInput::make('order')
                            ->label('Display Order')
                            ->numeric()
                            ->default(0)
                            ->rules(['min:0'])
                            ->helperText('Lower numbers appear first.'),

                        Toggle::make('is_active')
                            ->label('Is Active')
                            ->default(true)
                            ->helperText('Deactivate to hide this category from the website.'),
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
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('description')
                    ->label('Description (EN)')
                    ->limit(50)
                    ->tooltip(fn (string $state): string => $state)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('description_es') // Display Spanish description in table (toggleable)
                    ->label('Description (ES)')
                    ->limit(50)
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
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        true => 'Active',
                        false => 'Inactive',
                    ]),
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
            'index' => Pages\ListTherapeuticCategories::route('/'),
            'create' => Pages\CreateTherapeuticCategory::route('/create'),
            'edit' => Pages\EditTherapeuticCategory::route('/{record}/edit'),
        ];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();
        // Ensure English slug is generated/updated if name changed and slug is empty
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        return $data;
    }
}