<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product; // Corrected to Product model
use App\Models\Manufacturer; // Import Manufacturer model for relationship
use App\Models\DosageForm; // Import DosageForm model for relationship
use App\Models\TherapeuticCategory; // Import TherapeuticCategory model for relationship
use Filament\Forms\Form;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select; // For relationships and availability
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model; // For mutateFormData
use App\Models\User; // For creator/editor relationships
use Illuminate\Support\Str; // For slug auto-generation
use Filament\Forms\Set; // For auto-generating slug

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube'; // A good icon for products
    protected static ?string $navigationLabel = 'Products';
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

                Select::make('manufacturer_id')
                    ->label('Manufacturer')
                    ->relationship('manufacturer', 'name') // Link to Manufacturer model's 'name'
                    ->placeholder('Select a Manufacturer')
                    ->searchable()
                    ->preload()
                    ->nullable(),

                Select::make('dosage_form_id')
                    ->label('Dosage Form')
                    ->relationship('dosageForm', 'name') // Link to DosageForm model's 'name'
                    ->placeholder('Select a Dosage Form')
                    ->searchable()
                    ->preload()
                    ->nullable(),

                Select::make('therapeutic_category_id')
                    ->label('Therapeutic Category')
                    ->relationship('therapeuticCategory', 'name') // Link to TherapeuticCategory model's 'name'
                    ->placeholder('Select a Therapeutic Category')
                    ->searchable()
                    ->preload()
                    ->nullable(),

                RichEditor::make('description')
                    ->label('Product Description')
                    ->placeholder('A brief description of the product and its primary use case.')
                    ->toolbarButtons(['bold', 'italic', 'link', 'blockquote', 'strike', 'bulletList', 'orderedList'])
                    ->columnSpanFull(),

                RichEditor::make('benefits')
                    ->label('Benefits / Uses')
                    ->placeholder('Detailed benefits and uses of the product.')
                    ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                    ->columnSpanFull(),

                TextInput::make('ingredients') // New field for ingredients
                    ->label('Ingredients')
                    ->placeholder('e.g., Active ingredient A, Inactive ingredient B, Excipient C')
                    ->helperText('Enter ingredients as a comma-separated list.')
                    ->maxLength(65535) // TEXT type in DB supports up to 65535 characters
                    ->columnSpanFull(),

                Select::make('availability_status') // Availability field
                    ->label('Availability Status')
                    ->options([
                        'Available' => 'Available',
                        'Out of Stock' => 'Out of Stock',
                        'Discontinued' => 'Discontinued',
                        'Coming Soon' => 'Coming Soon',
                    ])
                    ->default('Available')
                    ->required(),

                FileUpload::make('product_image_path')
                    ->label('Product Image')
                    ->image()
                    ->directory('product-images') // Store images in storage/app/public/product-images
                    ->nullable()
                    ->columnSpanFull(),

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
                    ->defaultImageUrl(url('/images/default-product.png')), // Optional: A default image

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn (string $state): string => $state),

                TextColumn::make('manufacturer.name') // Display manufacturer name
                    ->label('Manufacturer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('dosageForm.name') // Display dosage form name
                    ->label('Dosage Form')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('therapeuticCategory.name') // Display therapeutic category name
                    ->label('Therapeutic Category')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Hidden by default

                TextColumn::make('availability_status')
                    ->label('Availability')
                    ->badge() // Display as a nice badge
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

    // Auto-populate created_by and updated_by, and slug
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        // Ensure slug is generated if not manually set
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();
        // Ensure slug is generated/updated if name changed and slug is empty
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        return $data;
    }
}