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
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Repeater; // IMPORTANT: Import Repeater
use Filament\Forms\Components\Hidden; // For Repeater order
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
                            ])->columns(1),

                        Tab::make('Spanish')
                            ->schema([
                                TextInput::make('name_es')
                                    ->label('Name (Spanish)')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true, table: Product::class, column: 'name_es')
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
                            ])->columns(1),
                    ])->columnSpanFull(),

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
                    ])->columns(2),


                    Section::make('Product Images')
    ->description('Upload multiple images for this product. Drag & drop to reorder.')
    ->collapsible()
    ->schema([
        Repeater::make('images') // Relationship name from Product model
            ->relationship('images') // Important: Connects to the HasMany relationship
            ->label('Gallery Images')
            ->columns(2)
            ->reorderableWithButtons() // Enables drag handles
            ->defaultItems(1) // Starts with one image field
            ->itemLabel(function (array $state): ?string {
    $path = $state['path'] ?? null;
    return is_string($path) ? basename($path) : null;
})

            ->orderColumn('order') // Enables drag-and-drop sorting using 'order' column
            ->addable() // Enables item adding
            ->minItems(0)
            ->maxItems(5)
            ->helperText('Maximum 5 images per product. First image will be used for listing thumbnail.')
            ->schema([
                FileUpload::make('path')
                    ->label('Image File')
                    ->image()
                    ->required()
                    ->directory('product-images')
                    ->columnSpan(1),
                TextInput::make('alt_text')
                    ->label('Alt Text')
                    ->maxLength(255)
                    ->placeholder('Description of image for SEO/accessibility')
                    ->columnSpan(1),
                Hidden::make('order') // Needed for ordering support
                    ->default(0),
            ]),
    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Updated to show the first image from the 'images' relationship
                ImageColumn::make('images.0.path') // Accesses the path of the first image in the collection
                    ->label('Image')
                    ->square()
                    ->height(60)
                    ->defaultImageUrl(url('/images/default-product.png')),

                TextColumn::make('name')
                    ->label('Name (EN)')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('name_es')
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
            // Add ProductImageRelationManager if you want to manage images on a separate tab.
            // For Repeater, it's embedded in the form directly.
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
        // Ensure English slug is generated/updated if name changed and slug is empty
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        return $data;
    }
}