<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsArticleResource\Pages;
use App\Filament\Resources\NewsArticleResource\RelationManagers;
use App\Models\NewsArticle;
use App\Models\NewsCategory;
use Filament\Forms\Form;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
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

class NewsArticleResource extends Resource
{
    protected static ?string $model = NewsArticle::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'News Articles';
    protected static ?string $navigationGroup = 'News Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Main tab structure for multilingual content
                Tabs::make('Article Content')
                    ->tabs([
                        Tab::make('English')
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true, table: NewsArticle::class, column: 'slug') // Corrected unique validation
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                                    ->label('Title (English)'),

                                TextInput::make('snippet')
                                    ->label('Snippet / Excerpt (English)')
                                    ->maxLength(255)
                                    ->helperText('A short summary for listings. If empty, will be generated from content.'),

                                RichEditor::make('content')
                                    ->label('Full Article Content (English)')
                                    ->required()
                                    ->toolbarButtons(['blockquote', 'bold', 'bulletList', 'codeBlock', 'h1', 'h2', 'h3', 'italic', 'link', 'orderedList', 'redo', 'strike', 'underline', 'undo'])
                                    ->fileAttachmentsDirectory('news-content-images'),
                            ])->columns(1),

                        Tab::make('Spanish')
                            ->schema([
                                TextInput::make('title_es')
                                    ->label('Title (Spanish)')
                                    ->maxLength(255)
                                    // CORRECTED UNIQUE VALIDATION: Removed duplicate ignoreRecord: true
                                    // And changed column to 'title_es' for uniqueness in Spanish titles if needed
                                    // If slug is *always* based on EN title, then uniqueness for ES title itself is just against other ES titles.
                                    ->unique(ignoreRecord: true, table: NewsArticle::class, column: 'title_es')
                                    ->helperText('Spanish title, will use English slug.'),

                                TextInput::make('snippet_es')
                                    ->label('Snippet / Excerpt (Spanish)')
                                    ->maxLength(255),

                                RichEditor::make('content_es')
                                    ->label('Full Article Content (Spanish)')
                                    ->toolbarButtons(['blockquote', 'bold', 'bulletList', 'codeBlock', 'h1', 'h2', 'h3', 'italic', 'link', 'orderedList', 'redo', 'strike', 'underline', 'undo'])
                                    ->fileAttachmentsDirectory('news-content-images'),
                            ])->columns(1),
                    ])->columnSpanFull(),

                // Separate section for common article details (not language specific)
                Section::make('Article Details')
                    ->schema([
                        Select::make('news_category_id')
                            ->label('Category')
                            ->relationship('newsCategory', 'name')
                            ->placeholder('Select a category')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        FileUpload::make('featured_image_path')
                            ->label('Featured Image')
                            ->image()
                            ->directory('news-images')
                            ->nullable()
                            ->columnSpanFull(),

                        TextInput::make('slug') // Slug should not be in tabs, as it's common
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true, table: NewsArticle::class) // Default unique on 'slug' column
                            ->helperText('A unique URL-friendly identifier. Auto-generated from title.'),

                        DateTimePicker::make('published_at')
                            ->label('Publish Date')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->timezone(config('app.timezone', 'Asia/Kolkata'))
                            ->default(now())
                            ->nullable(),

                        Toggle::make('is_active')
                            ->label('Is Active (Published)')
                            ->default(true)
                            ->helperText('Toggle to publish or unpublish the article.'),

                        Toggle::make('is_featured')
                            ->label('Show on Homepage (Featured)')
                            ->default(false)
                            ->helperText('Enable to display this article in the "Latest News & Insights" section on the homepage.'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image_path')
                    ->label('Image')
                    ->square()
                    ->height(60)
                    ->defaultImageUrl(url('/images/default-news.png')),

                TextColumn::make('title')
                    ->label('Title (EN)')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn (string $state): string => $state),

                TextColumn::make('title_es')
                    ->label('Title (ES)')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn (string $state): string => $state)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('newsCategory.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('published_at')
                    ->label('Publish Date')
                    ->dateTime('d M Y H:i')
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
                Tables\Filters\SelectFilter::make('news_category_id')
                    ->label('Category')
                    ->relationship('newsCategory', 'name')
                    ->multiple()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Published Status')
                    ->boolean()
                    ->trueLabel('Published')
                    ->falseLabel('Draft')
                    ->default(true),
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
            ->defaultSort('published_at', 'desc');
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

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        if (empty($data['snippet']) && !empty($data['content'])) {
            $data['snippet'] = Str::limit(strip_tags($data['content']), 150);
        }
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