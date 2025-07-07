<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Filament\Resources\PageResource\RelationManagers;
use App\Models\Page; // Corrected to Page model
use Filament\Forms\Form;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section; // For organizing form fields
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

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate'; // Icon for general pages
    protected static ?string $navigationLabel = 'Pages';
    protected static ?string $navigationGroup = 'Site Content'; // Group with Services

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Page Content')
                    ->description('Main content and visual for the page.')
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

                        FileUpload::make('featured_image_path')
                            ->label('Featured Image / Banner')
                            ->image()
                            ->directory('page-images') // Store images in storage/app/public/page-images
                            ->nullable()
                            ->columnSpanFull(),

                        RichEditor::make('content')
                            ->label('Page Content')
                            ->placeholder('Enter the main content for this page here...')
                            ->toolbarButtons([
                                'blockquote', 'bold', 'bulletList', 'codeBlock', 'h1', 'h2', 'h3', 'italic',
                                'link', 'orderedList', 'redo', 'strike', 'underline', 'undo',
                            ])
                            ->columnSpanFull()
                            ->fileAttachmentsDirectory('page-content-attachments'), // Images embedded in content will go here
                    ])->columns(2), // Layout this section in 2 columns

                Section::make('SEO Settings')
                    ->description('Optimize this page for search engines.')
                    ->collapsible() // Make this section collapsible
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(255)
                            ->helperText('Appears in browser tab and search results. Max 60-70 chars.'),
                        RichEditor::make('meta_description') // Using RichEditor for potentially longer text
                            ->label('Meta Description')
                            ->placeholder('A concise summary of the page content for search engines.')
                            ->maxLength(255)
                            ->helperText('Max 150-160 characters. Avoid meta keywords for modern SEO.')
                            ->toolbarButtons(['bold', 'italic', 'link']), // Simple toolbar for meta description
                    ])->columns(1), // Layout this section in 1 column (full width)

                Section::make('Status')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Is Active (Publish Page)')
                            ->default(true)
                            ->helperText('Toggle to publish or unpublish this page.'),
                    ])->columns(1), // Layout this section in 1 column
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Hidden by default
                ImageColumn::make('featured_image_path')
                    ->label('Image')
                    ->square()
                    ->height(40) // Smaller image in table
                    ->defaultImageUrl(url('/images/default-page.png')), // Optional: A default image
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
            ->defaultSort('title', 'asc'); // Default sort by title
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }

    // Auto-populate created_by and updated_by
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        // Ensure slug is generated if not manually set
        if (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }
        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();
        // Ensure slug is generated/updated if title changed and slug is empty
        if (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }
        return $data;
    }
}