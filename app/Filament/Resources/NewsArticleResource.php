<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsArticleResource\Pages;
use App\Filament\Resources\NewsArticleResource\RelationManagers;
use App\Models\NewsArticle; // Corrected to NewsArticle model
use App\Models\NewsCategory; // Import NewsCategory model for relationships
use Filament\Forms\Form;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload; // For featured image
use Filament\Forms\Components\Select; // For category dropdown
use Filament\Forms\Components\DateTimePicker; // For published_at
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn; // For image in table
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model; // For mutateFormData
use App\Models\User; // For creator/editor relationships
use Illuminate\Support\Str; // For slug auto-generation
use Filament\Forms\Set; // For auto-generating slug

class NewsArticleResource extends Resource
{
    protected static ?string $model = NewsArticle::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text'; // Icon for news articles
    protected static ?string $navigationLabel = 'News Articles';
    protected static ?string $navigationGroup = 'News Management'; // Group with News Categories

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->live(onBlur: true) // Update slug as title is typed (on blur)
                    ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                    ->columnSpanFull()
                    ->autofocus(),

                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('A unique URL-friendly identifier. Auto-generated from title.'),

                Select::make('news_category_id')
                    ->label('Category')
                    ->relationship('newsCategory', 'name') // Link to NewsCategory model's 'name'
                    ->placeholder('Select a category')
                    ->searchable() // Allow searching categories
                    ->preload() // Load all categories upfront
                    ->nullable(), // Allow articles to be uncategorized

                FileUpload::make('featured_image_path')
                    ->label('Featured Image')
                    ->image()
                    ->directory('news-images') // Store images in storage/app/public/news-images
                    ->nullable() // Image is optional for an article
                    ->columnSpanFull(),

                // You can use a separate TextInput for snippet, or generate it
                TextInput::make('snippet')
                    ->label('Snippet / Excerpt')
                    ->maxLength(255)
                    ->helperText('A short summary shown in listings. If empty, will be generated from content.')
                    ->columnSpanFull(),

                RichEditor::make('content')
                    ->label('Full Article Content')
                    ->required()
                    ->toolbarButtons([
                        'blockquote', 'bold', 'bulletList', 'codeBlock', 'h1', 'h2', 'h3', 'italic',
                        'link', 'orderedList', 'strike', 'underline',
                    ])
                    ->columnSpanFull()
                    ->fileAttachmentsDirectory('news-content-images'), // Images embedded in content will go here

                DateTimePicker::make('published_at')
                    ->label('Publish Date')
                    ->native(false) // Use Filament's date picker, not native browser
                    ->displayFormat('d/m/Y H:i') // Display format
                    ->timezone('Asia/Kolkata') // Set your timezone if different, e.g., 'America/Santo_Domingo'
                    ->default(now()) // Default to current date/time
                    ->nullable(), // Can be left null for draft or future publishing

                Toggle::make('is_active')
                    ->label('Is Active (Published)')
                    ->default(true)
                    ->helperText('Toggle to publish or unpublish the article.'),

                Toggle::make('is_featured')
                    ->label('Show on Homepage (Featured)')
                    ->default(false)
                    ->helperText('Enable to display this article in the "Latest News & Insights" section on the homepage.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image_path')
                    ->label('Image')
                    ->square() // Make image square
                    ->height(60) // Size for table preview
                    ->defaultImageUrl(url('/images/default-news.png')), // Optional: Default image if none

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50) // Truncate title if too long
                    ->tooltip(fn (string $state): string => $state), // Show full title on hover

                TextColumn::make('newsCategory.name') // Display category name via relationship
                    ->label('Category')
                    ->searchable() // Allow searching by category name
                    ->sortable(),

                TextColumn::make('published_at')
                    ->label('Publish Date')
                    ->dateTime('d M Y H:i') // Format date for display
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
                // Add filters here later if needed (e.g., by category, by featured status)
                Tables\Filters\SelectFilter::make('news_category_id')
                    ->label('Category')
                    ->relationship('newsCategory', 'name')
                    ->multiple(), // Allow selecting multiple categories for filtering
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Published Status')
                    ->boolean()
                    ->trueLabel('Published')
                    ->falseLabel('Draft')
                    ->default(true), // Show published by default
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
            ->defaultSort('published_at', 'desc'); // Default sort by latest articles
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
            'index' => Pages\ListNewsArticles::route('/'),
            'create' => Pages\CreateNewsArticle::route('/create'),
            'edit' => Pages\EditNewsArticle::route('/{record}/edit'),
        ];
    }

    // Auto-populate created_by and updated_by
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        // Generate snippet if not provided
        if (empty($data['snippet']) && !empty($data['content'])) {
            $data['snippet'] = Str::limit(strip_tags($data['content']), 150);
        }
        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();
        // Re-generate snippet if content changed and snippet is empty
        if (empty($data['snippet']) && !empty($data['content'])) {
            $data['snippet'] = Str::limit(strip_tags($data['content']), 150);
        }
        return $data;
    }
}