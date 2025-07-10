<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Models\Manufacturer;
use App\Models\DosageForm;
use App\Models\TherapeuticCategory;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
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
use Illuminate\Support\Str;
use Filament\Forms\Set;
use Filament\Forms\Get;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Products';
    protected static ?string $navigationGroup = 'Product Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Main tab structure for multilingual content
                Tabs::make('Product Content')
                    ->tabs([
                        Tab::make('English')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                                    ->label('Name (English)'),

                                RichEditor::make('description')
                                    ->label('Product Description (English)')
                                    ->toolbarButtons(['bold', 'italic', 'link', 'blockquote', 'strike', 'bulletList', 'orderedList']),

                                RichEditor::make('benefits')
                                    ->label('Benefits / Uses (English)')
                                    ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList']),

                                TextInput::make('ingredients')
                                    ->label('Ingredients (English)')
                                    ->placeholder('e.g., Active ingredient A, Inactive ingredient B, Excipient C')
                                    ->helperText('Enter ingredients as a comma-separated list.')
                                    ->maxLength(65535),
                            ])->columns(1), // Use 1 column for content in tabs for readability

                        Tab::make('Spanish')
                            ->schema([
                                TextInput::make('name_es')
                                    ->label('Name (Spanish)')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true, table: Product::class, column: 'name_es') // Unique in Spanish names
                                    ->helperText('Spanish name.'),

                                RichEditor::make('description_es')
                                    ->label('Product Description (Spanish)')
                                    ->toolbarButtons(['bold', 'italic', 'link', 'blockquote', 'strike', 'bulletList', 'orderedList']),

                                RichEditor::make('benefits_es')
                                    ->label('Benefits / Uses (Spanish)')
                                    ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList']),

                                TextInput::make('ingredients_es')
                                    ->label('Ingredients (Spanish)')
                                    ->placeholder('p. ej., Ingrediente activo A, Ingrediente inactivo B, Excipiente C')
                                    ->helperText('Ingrese los ingredientes como una lista separada por comas.')
                                    ->maxLength(65535),
                            ])->columns(1), // Use 1 column for content in tabs
                    ])->columnSpanFull(), // Make tabs span full width

                // Separate section for common product details (not language specific)
                Section::make('Product Details')
                    ->schema([
                        Select::make('manufacturer_id')
                            ->label('Manufacturer')
                            ->relationship('manufacturer', 'name')
                            ->placeholder('Select a Manufacturer')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Select::make('dosage_form_id')
                            ->label('Dosage Form')
                            ->relationship('dosageForm', 'name')
                            ->placeholder('Select a Dosage Form')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Select::make('therapeutic_category_id')
                            ->label('Therapeutic Category')
                            ->relationship('therapeuticCategory', 'name')
                            ->placeholder('Select a Therapeutic Category')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        FileUpload::make('product_image_path')
                            ->label('Product Image')
                            ->image()
                            ->directory('product-images')
                            ->nullable()
                            ->columnSpanFull(),

                        TextInput::make('slug') // Slug should not be in tabs, as it's common
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true, table: Product::class)
                            ->helperText('A unique URL-friendly identifier. Auto-generated from English name.'),

                        Select::make('availability_status')
                            ->label('Availability Status')
                            ->options([
                                'Available' => 'Available',
                                'Out of Stock' => 'Out of Stock',
                                'Discontinued' => 'Discontinued',
                                'Coming Soon' => 'Coming Soon',
                            ])
                            ->default('Available')
                            ->required(),

                        TextInput::make('order')
                            ->label('Display Order')
                            ->numeric()
                            ->default(0)
                            ->rules(['min:0'])
                            ->helperText('Lower numbers appear first.'),

                        Toggle::make('is_active')
                            ->label('Is Active (Display on Website)')
                            ->default(true)
                            ->helperText('Toggle to hide/show this product on the public website.'),
                    ])->columns(2), // Layout this section in 2 columns
                ]);
        }

        public static function table(Table $table): Table
        {
            return $table
                ->columns([
                    ImageColumn::make('product_image_path')
                        ->label('Image')
                        ->square()
                        ->height(60)
                        ->defaultImageUrl(url('/images/default-product.png')),

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
                    TextColumn::make('manufacturer.name')
                        ->label('Manufacturer (EN)')
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('dosageForm.name')
                        ->label('Dosage Form (EN)')
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('therapeuticCategory.name')
                        ->label('Therapeutic Category (EN)')
                        ->searchable()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('availability_status')
                        ->label('Availability')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'Available' => 'success',
                            'Out of Stock' => 'danger',
                            'Discontinued' => 'warning',
                            'Coming Soon' => 'info',
                            default => 'secondary',
                        }),
                    ToggleColumn::make('is_active')
                        ->label('Active'),
                    TextColumn::make('order')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
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
                    Tables\Filters\SelectFilter::make('manufacturer_id')
                        ->label('Manufacturer')
                        ->relationship('manufacturer', 'name')
                        ->multiple()
                        ->preload(),
                    Tables\Filters\SelectFilter::make('dosage_form_id')
                        ->label('Dosage Form')
                        ->relationship('dosageForm', 'name')
                        ->multiple()
                        ->preload(),
                    Tables\Filters\SelectFilter::make('therapeutic_category_id')
                        ->label('Therapeutic Category')
                        ->relationship('therapeuticCategory', 'name')
                        ->multiple()
                        ->preload(),
                    Tables\Filters\SelectFilter::make('availability_status')
                        ->label('Availability')
                        ->options([
                            'Available' => 'Available',
                            'Out of Stock' => 'Out of Stock',
                            'Discontinued' => 'Discontinued',
                            'Coming Soon' => 'Coming Soon',
                        ]),
                    Tables\Filters\TernaryFilter::make('is_active')
                        ->label('Active Status')
                        ->boolean()
                        ->trueLabel('Active')
                        ->falseLabel('Inactive')
                        ->default(true),
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
                'index' => Pages\ListProducts::route('/'),
                'create' => Pages\CreateProduct::route('/create'),
                'edit' => Pages\EditProduct::route('/{record}/edit'),
            ];
        }

        public static function mutateFormDataBeforeCreate(array $data): array
        {
            $data['created_by'] = auth()->id();
            if (empty($data['slug']) && !empty($data['title'])) {
                $data['slug'] = Str::slug($data['title']);
            }
            return $data;
        }

        public static function mutateFormDataBeforeSave(array $data): array
        {
            $data['updated_by'] = auth()->id();
            // Mutate snippet_es if content_es changed and snippet_es is empty
            if (empty($data['snippet_es']) && !empty($data['content_es'])) {
                $data['snippet_es'] = Str::limit(strip_tags($data['content_es']), 150);
            }
            // Ensure English snippet is also mutated if content changed and snippet is empty
            if (empty($data['snippet']) && !empty($data['content'])) {
                $data['snippet'] = Str::limit(strip_tags($data['content']), 150);
            }
            // Slug is derived from EN title, no need for ES slug.
            return $data;
        }
    }
    