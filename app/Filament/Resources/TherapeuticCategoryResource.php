<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TherapeuticCategoryResource\Pages;
use App\Filament\Resources\TherapeuticCategoryResource\RelationManagers;
use App\Models\TherapeuticCategory; // Corrected to TherapeuticCategory model
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
use Illuminate\Support\Str; // For slug auto-generation
use Filament\Forms\Set; // For auto-generating slug

class TherapeuticCategoryResource extends Resource
{
    protected static ?string $model = TherapeuticCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open'; // A suitable icon for categories
    protected static ?string $navigationLabel = 'Therapeutic Categories';
    protected static ?string $navigationGroup = 'Product Management'; // Group with Manufacturers and Dosage Forms

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->live(onBlur: true) // Update slug as name is typed (on blur)
                    ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                    ->columnSpanFull()
                    ->autofocus(),

                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('A unique URL-friendly identifier. Auto-generated from name.'),

                RichEditor::make('description')
                    ->label('Description')
                    ->placeholder('A brief description of this therapeutic category.')
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
                    ->helperText('Deactivate to hide this category from the website.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Hidden by default
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
            'index' => Pages\ListTherapeuticCategories::route('/'),
            'create' => Pages\CreateTherapeuticCategory::route('/create'),
            'edit' => Pages\EditTherapeuticCategory::route('/{record}/edit'),
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