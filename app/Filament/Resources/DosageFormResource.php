<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DosageFormResource\Pages;
use App\Filament\Resources\DosageFormResource\RelationManagers;
use App\Models\DosageForm;
use Filament\Forms\Form;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
// IMPORTANT: IconColumn is NOT used here to avoid the SVG error.
// Make sure this line is NOT present if you commented it out before:
// use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn; // Keep TextColumn
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // For creator/editor relationships

class DosageFormResource extends Resource
{
    protected static ?string $model = DosageForm::class;

    // Using a basic Heroicon to avoid previous SVG issues.
    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Dosage Forms';
    protected static ?string $navigationGroup = 'Product Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull()
                    ->autofocus(),

                // We keep this field to store the Font Awesome class for frontend use
                TextInput::make('icon_class')
                    ->label('Font Awesome Icon Class')
                    ->placeholder('e.g., fa-solid fa-syringe, fa-pills')
                    ->maxLength(255)
                    ->helperText('Find icons at fontawesome.com. Use "fa-solid fa-iconname".'),

                RichEditor::make('description')
                    ->label('Description')
                    ->placeholder('A brief description of this dosage form.')
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
                    ->helperText('Deactivate to hide this dosage form from the website.'),

                Toggle::make('is_featured')
                    ->label('Show on Homepage (Featured)')
                    ->default(false)
                    ->helperText('Enable to display this dosage form in the "A Comprehensive Product Portfolio" section on the homepage.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                // The 'icon_class' column is TEMPORARILY REMOVED FROM THE TABLE
                // to bypass the SvgNotFound error.
                // We will re-evaluate displaying it later if needed.

                TextColumn::make('description')
                    ->limit(70)
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