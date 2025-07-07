<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NavigationItemResource\Pages;
use App\Filament\Resources\NavigationItemResource\RelationManagers;
use App\Models\NavigationItem; // Corrected to NavigationItem model
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section; // For organizing forms
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model; // For mutateFormData
use App\Models\User; // For creator/editor relationships

// IMPORTANT: Import these for conditional form logic
use Filament\Forms\Set;
use Filament\Forms\Get;

class NavigationItemResource extends Resource
{
    protected static ?string $model = NavigationItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3-bottom-left'; // Icon for navigation
    protected static ?string $navigationLabel = 'Navigation Menus';
    protected static ?string $navigationGroup = 'Site Content'; // Group with Pages, Services

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Navigation Item Details')
                    ->schema([
                        TextInput::make('label')
                            ->required()
                            ->maxLength(255)
                            ->helperText('The text displayed in the menu (e.g., "About Us", "Our Products").')
                            ->columnSpanFull(),

                        Select::make('type')
                            ->options([
                                'home_index' => 'Homepage', // Specific link for home
                                'products_index' => 'Products Page', // Specific link for products index
                                'news_index' => 'News Page', // Specific link for news index
                                'contact_index' => 'Contact Page', // Specific link for contact page
                                'page' => 'CMS Page',
                                'news_category' => 'News Category',
                                'therapeutic_category' => 'Product Therapeutic Category',
                                'dosage_form' => 'Product Dosage Form',
                                'homepage_section' => 'Homepage Section (Anchor)', // For #network, #services etc.
                                'custom_url' => 'Custom URL',
                            ])
                            ->required()
                            ->reactive() // Make the form reactive to changes in this field
                            ->afterStateUpdated(fn ($state, Set $set) => $set('custom_url', null)) // Clear custom_url if type changes
                            ->helperText('Select the type of link this menu item will be.'),

                        // Conditional Fields based on 'type'
                        TextInput::make('custom_url')
                            ->label('Custom URL')
                            ->url()
                            ->visible(fn (Get $get): bool => $get('type') === 'custom_url' || $get('type') === 'homepage_section')
                            ->required(fn (Get $get): bool => $get('type') === 'custom_url') // Required only if type is custom_url
                            ->placeholder(fn (Get $get): string => $get('type') === 'homepage_section' ? '#network' : 'https://example.com/'),

                        Select::make('page_id')
                            ->label('Select CMS Page')
                            ->relationship('page', 'title') // Link to Page model's 'title'
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('type') === 'page')
                            ->required(fn (Get $get): bool => $get('type') === 'page'),

                        Select::make('news_category_id')
                            ->label('Select News Category')
                            ->relationship('newsCategory', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('type') === 'news_category')
                            ->required(fn (Get $get): bool => $get('type') === 'news_category'),

                        Select::make('therapeutic_category_id')
                            ->label('Select Therapeutic Category')
                            ->relationship('therapeuticCategory', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('type') === 'therapeutic_category')
                            ->required(fn (Get $get): bool => $get('type') === 'therapeutic_category'),

                        Select::make('dosage_form_id')
                            ->label('Select Dosage Form')
                            ->relationship('dosageForm', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('type') === 'dosage_form')
                            ->required(fn (Get $get): bool => $get('type') === 'dosage_form'),

                        Select::make('location')
                            ->options([
                                'header' => 'Header Navigation',
                                'footer_navigate' => 'Footer - Navigate Column',
                                'footer_legal' => 'Footer - Legal Column',
                                // Add more locations as needed, e.g., 'mobile_menu' if different structure
                            ])
                            ->required()
                            ->default('header')
                            ->helperText('Where this menu item will appear on the website.'),

                        TextInput::make('order')
                            ->label('Display Order')
                            ->numeric()
                            ->default(0)
                            ->rules(['min:0'])
                            ->helperText('Lower numbers appear first within its location.'),

                        Toggle::make('is_active')
                            ->label('Is Active')
                            ->default(true)
                            ->helperText('Toggle to display or hide this menu item.'),
                    ])->columns(2), // Layout main details in 2 columns
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('location')
                    ->badge()
                    ->sortable(),
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
                Tables\Filters\SelectFilter::make('location')
                    ->options([
                        'header' => 'Header Navigation',
                        'footer_navigate' => 'Footer - Navigate Column',
                        'footer_legal' => 'Footer - Legal Column',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'home_index' => 'Homepage',
                        'products_index' => 'Products Page',
                        'news_index' => 'News Page',
                        'contact_index' => 'Contact Page',
                        'page' => 'CMS Page',
                        'news_category' => 'News Category',
                        'therapeutic_category' => 'Product Therapeutic Category',
                        'dosage_form' => 'Product Dosage Form',
                        'homepage_section' => 'Homepage Section (Anchor)',
                        'custom_url' => 'Custom URL',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
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
            'index' => Pages\ListNavigationItems::route('/'),
            'create' => Pages\CreateNavigationItem::route('/create'),
            'edit' => Pages\EditNavigationItem::route('/{record}/edit'),
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