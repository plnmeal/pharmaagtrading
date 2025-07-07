<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsCategoryResource\Pages;
use App\Filament\Resources\NewsCategoryResource\RelationManagers;
use App\Models\NewsCategory;
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
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Str;
use Filament\Forms\Set; // Add this line

class NewsCategoryResource extends Resource // <--- KEEP ONLY THIS ONE!
{
    protected static ?string $model = NewsCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag'; // A good icon for categories
    protected static ?string $navigationLabel = 'News Categories';
    protected static ?string $navigationGroup = 'News Management'; // A new group for news-related items

    // The form() method (which starts below) should be part of this ONE class
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->live(onBlur: true)
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
                    ->placeholder('A brief description of this news category.')
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
            'index' => Pages\ListNewsCategories::route('/'),
            'create' => Pages\CreateNewsCategory::route('/create'),
            'edit' => Pages\EditNewsCategory::route('/{record}/edit'),
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