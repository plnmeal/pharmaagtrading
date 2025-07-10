<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Filament\Resources\SettingResource\RelationManagers;
use App\Models\Setting; // IMPORTANT: Ensure this is imported
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Tabs; // IMPORT THIS
use Filament\Forms\Components\Tabs\Tab; // IMPORT THIS
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model; // For mutateFormData
use Filament\Notifications\Notification; // For notifications

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationGroup = 'Site Management';
    protected static ?string $label = 'Global Site Settings'; // Label for single record
    protected static ?string $pluralLabel = 'Global Site Settings'; // Plural label (even if only one)

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Content Tabs')
                    ->tabs([
                        Tab::make('English Content')
                            ->schema([
                                Section::make('Homepage Hero Section (English)')
                                    ->schema([
                                        TextInput::make('hero_title')->label('Hero Title (English)')->maxLength(255)->placeholder('Your Gateway to Quality Pharma Solutions'),
                                        RichEditor::make('hero_subtitle')->label('Hero Subtitle (English)')->toolbarButtons(['bold', 'italic', 'strike', 'underline', 'link'])->placeholder('Powering the health of the Dominican Republic...'),
                                        FileUpload::make('hero_image_path')->label('Hero Background Image')->image()->directory('settings'),
                                    ]),
                                Section::make('Call to Action (CTA) Section (English)')
                                    ->schema([
                                        TextInput::make('cta_title')->label('CTA Title (English)')->maxLength(255)->placeholder('Build the Future of Healthcare, Together.'),
                                        RichEditor::make('cta_description')->label('CTA Description (English)')->toolbarButtons(['bold', 'italic', 'strike', 'underline', 'link'])->placeholder('Join our network of leading manufacturers...'),
                                        TextInput::make('cta_button_text')->label('CTA Button Text (English)')->maxLength(255)->placeholder('Become a Partner'),
                                        TextInput::make('cta_button_link')->label('CTA Button Link')->url()->placeholder('mailto:partners@pharmaagtrading.net'),
                                        FileUpload::make('cta_background_image_path')->label('CTA Background Image')->image()->directory('settings'),
                                    ]),
                                Section::make('Contact Information (English Address)')
                                    ->schema([
                                        TextInput::make('contact_address')->label('Address (English)')->maxLength(255)->placeholder('Av. John F. Kennedy, Santo Domingo, D.N., 10122'),
                                        TextInput::make('contact_email')->label('Primary Contact Email')->email()->maxLength(255)->placeholder('info.do@pharmaagtrading.net'),
                                        TextInput::make('contact_phone')->label('Phone Number')->tel()->maxLength(255)->placeholder('+1 (809) 123-4567'),
                                    ]),
                                Section::make('General Site Information (English)')
                                    ->schema([
                                        TextInput::make('site_name')->label('Site Name / Logo Text (English)')->required()->maxLength(255)->placeholder('pharmaagtrading'),
                                        RichEditor::make('site_description')->label('Site Description (English)')->toolbarButtons(['bold', 'italic', 'link'])->placeholder('Redefining pharmaceutical distribution...'),
                                         TextInput::make('subheader_text')->label('Subheader Text (English)')->maxLength(255)->placeholder('Quality & Trust in Every Delivery'), // NEW FIELD HERE

                                    ]),
                            ])->columns(2),

                        Tab::make('Spanish Content')
                            ->schema([
                                Section::make('Homepage Hero Section (Spanish)')
                                    ->schema([
                                        TextInput::make('hero_title_es')->label('Hero Title (Spanish)')->maxLength(255)->placeholder('Tu Puerta de Entrada a Soluciones Farmacéuticas de Calidad'),
                                        RichEditor::make('hero_subtitle_es')->label('Hero Subtitle (Spanish)')->toolbarButtons(['bold', 'italic', 'strike', 'underline', 'link'])->placeholder('Impulsando la salud de la República Dominicana...'),
                                    ]),
                                Section::make('Call to Action (CTA) Section (Spanish)')
                                    ->schema([
                                        TextInput::make('cta_title_es')->label('CTA Title (Spanish)')->maxLength(255)->placeholder('Construyamos el Futuro de la Salud, Juntos.'),
                                        RichEditor::make('cta_description_es')->label('CTA Description (Spanish)')->toolbarButtons(['bold', 'italic', 'strike', 'underline', 'link'])->placeholder('Únete a nuestra red de fabricantes...'),
                                        TextInput::make('cta_button_text_es')->label('CTA Button Text (Spanish)')->maxLength(255)->placeholder('Conviértete en Socio'),
                                    ]),
                                Section::make('Contact Information (Spanish Address)')
                                    ->schema([
                                        TextInput::make('contact_address_es')->label('Address (Spanish)')->maxLength(255)->placeholder('Av. John F. Kennedy, Santo Domingo, D.N., 10122'),
                                    ]),
                                Section::make('General Site Information (Spanish)')
                                    ->schema([
                                        TextInput::make('site_name_es')->label('Site Name / Logo Text (Spanish)')->maxLength(255)->placeholder('pharmaagtrading'),
                                        RichEditor::make('site_description_es')->label('Site Description (Spanish)')->toolbarButtons(['bold', 'italic', 'link'])->placeholder('Redefiniendo la distribución farmacéutica...'),
                                         TextInput::make('subheader_text_es')->label('Subheader Text (Spanish)')->maxLength(255)->placeholder('Calidad y Confianza en Cada Entrega'), // NEW FIELD HERE
                                    ]),
                            ])->columns(2),
                    ]),

                Section::make('Social Media Links')
                    ->description('Links to your company\'s social media profiles.')
                    ->collapsible()
                    ->schema([
                        TextInput::make('facebook_url')->label('Facebook URL')->url()->placeholder('https://facebook.com/pharmaagtrading'),
                        TextInput::make('twitter_url')->label('Twitter URL')->url()->placeholder('https://twitter.com/pharmaagtrading'),
                        TextInput::make('linkedin_url')->label('LinkedIn URL')->url()->placeholder('https://linkedin.com/company/pharmaagtrading'),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Only display primary site name in the table, as it's a singleton resource
                TextColumn::make('site_name')->label('Site Name'),
                TextColumn::make('site_name_es')->label('Site Name (ES)')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->dateTime()->sortable()->label('Last Updated'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // No delete action here, as it's a singleton
            ])
            ->bulkActions([
                // No bulk actions for a singleton
            ]);
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
            'index' => Pages\ListSettings::route('/'),
            'edit' => Pages\EditSetting::route('/{record}/edit'), // Only edit page needed
        ];
    }

    // --- Permissions to enforce single record ---
    public static function canCreate(): bool
    {
        // Only allow creating if no record exists, otherwise prevent
        return ! Setting::exists();
    }

    public static function canDelete(Model $record): bool
    {
        return false; // Prevent deleting any record
    }

    public static function canDeleteAny(): bool
    {
        return false; // Prevent bulk deleting
    }
    // --- END Permissions ---

    // Auto-populate updated_by (created_by handled by create() if record doesn't exist)
    public static function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_at'] = now(); // Ensure updated_at is explicitly set, if not handled by update()
        return $data;
    }

    // --- Custom logic for saving/updating the single record ---
    // Filament's Resource will handle saving/updating automatically via EditAction.
    // We ensure the single record exists upon entering the module.
    // The mount() method in the resource's List/Edit page handles initial data.
    // For a true singleton, we usually override ListSettings to redirect to edit.
}