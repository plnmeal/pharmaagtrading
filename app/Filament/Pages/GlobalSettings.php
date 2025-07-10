<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;

class GlobalSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationGroup = 'Site Management';
    protected static string $view = 'filament.pages.global-settings';

    protected static ?string $title = 'Global Site Settings (New)';

    public ?array $data = [];

    protected function getFormModel(): Model|string|null
    {
        return Setting::class;
    }

    protected function afterFormInitialized(): void
    {
        // Create the settings record if it doesn't exist
        $settings = Setting::find(1);

        if (! $settings) {
            $settings = $this->createDefaultSettings();
        }

        if ($settings) {
            $this->form->fill($settings->toArray());

            Notification::make()
                ->title('Form loaded with existing settings.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Settings record not found. Form will be empty.')
                ->danger()
                ->send();
        }
    }

    private function createDefaultSettings(): ?Setting
    {
        try {
            $settings = Setting::create([
                'id' => 1,
                'site_name' => 'pharmaagtrading',
                'site_name_es' => 'pharmaagtrading',
                'hero_title' => 'Your Gateway to Quality Pharma Solutions',
                'hero_title_es' => 'Tu Puerta de Entrada a Soluciones Farmacéuticas de Calidad',
                'hero_subtitle' => 'Powering the health of the Dominican Republic...',
                'hero_subtitle_es' => 'Impulsando la salud de la República Dominicana...',
                'site_description' => 'Redefining pharmaceutical distribution with technology...',
                'site_description_es' => 'Redefiniendo la distribución farmacéutica...',
                'contact_address' => 'Av. John F. Kennedy,<br>Santo Domingo, D.N., 10122',
                'contact_address_es' => 'Av. John F. Kennedy,<br>Santo Domingo, D.N., 10122',
                'contact_email' => 'info.do@pharmaagtrading.net',
                'contact_phone' => '+18091234567',
                'cta_title' => 'Build the Future of Healthcare, Together.',
                'cta_title_es' => 'Construyamos el Futro de la Salud, Juntos.',
                'cta_description' => 'Join our network of leading manufacturers and healthcare providers.',
                'cta_description_es' => 'Únete a nuestra red de fabricantes y proveedores de atención médica líderes.',
                'cta_button_text' => 'Become a Partner',
                'cta_button_text_es' => 'Conviértete en Socio',
                'cta_button_link' => 'mailto:partners@pharmaagtrading.net',
                'facebook_url' => 'https://facebook.com/pharmaagtrading',
                'twitter_url' => 'https://twitter.com/pharmaagtrading',
                'linkedin_url' => 'https://linkedin.com/company/pharmaagtrading',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Notification::make()
                ->title('Initial settings record created successfully!')
                ->success()
                ->send();

            return $settings;
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error creating initial settings record!')
                ->body('Details: ' . $e->getMessage())
                ->danger()
                ->send();

            return null;
        }
    }

    protected function getFormSchema(): array
    {
        return [
            Tabs::make('Content Tabs')->tabs([
                Tab::make('English Content')->schema([
                    Section::make('Homepage Hero Section (English)')->schema([
                        TextInput::make('hero_title')->label('Hero Title (English)'),
                        RichEditor::make('hero_subtitle')->label('Hero Subtitle (English)')->toolbarButtons(['bold', 'italic', 'underline']),
                        FileUpload::make('hero_image_path')->label('Hero Background Image')->image()->directory('settings')->nullable(),
                    ])->columns(1),

                    Section::make('Call to Action (CTA) Section (English)')->schema([
                        TextInput::make('cta_title')->label('CTA Title (English)'),
                        RichEditor::make('cta_description')->label('CTA Description (English)')->toolbarButtons(['bold', 'italic', 'underline']),
                        TextInput::make('cta_button_text')->label('CTA Button Text (English)'),
                        TextInput::make('cta_button_link')->label('CTA Button Link')->url(),
                        FileUpload::make('cta_background_image_path')->label('CTA Background Image')->image()->directory('settings')->nullable(),
                    ])->columns(1),

                    Section::make('Contact Information (English Address)')->schema([
                        TextInput::make('contact_address')->label('Address (English)'),
                        TextInput::make('contact_email')->label('Email')->email(),
                        TextInput::make('contact_phone')->label('Phone')->tel(),
                    ])->columns(1),

                    Section::make('General Site Information (English)')->schema([
                        TextInput::make('site_name')->label('Site Name (English)'),
                        RichEditor::make('site_description')->label('Site Description (English)')->toolbarButtons(['bold', 'italic']),
                    ])->columns(1),
                ]),

                Tab::make('Spanish Content')->schema([
                    Section::make('Homepage Hero Section (Spanish)')->schema([
                        TextInput::make('hero_title_es')->label('Hero Title (Spanish)'),
                        RichEditor::make('hero_subtitle_es')->label('Hero Subtitle (Spanish)')->toolbarButtons(['bold', 'italic', 'underline']),
                    ])->columns(1),

                    Section::make('Call to Action (CTA) Section (Spanish)')->schema([
                        TextInput::make('cta_title_es')->label('CTA Title (Spanish)'),
                        RichEditor::make('cta_description_es')->label('CTA Description (Spanish)')->toolbarButtons(['bold', 'italic', 'underline']),
                        TextInput::make('cta_button_text_es')->label('CTA Button Text (Spanish)'),
                    ])->columns(1),

                    Section::make('Contact Information (Spanish Address)')->schema([
                        TextInput::make('contact_address_es')->label('Address (Spanish)'),
                    ])->columns(1),

                    Section::make('General Site Information (Spanish)')->schema([
                        TextInput::make('site_name_es')->label('Site Name (Spanish)'),
                        RichEditor::make('site_description_es')->label('Site Description (Spanish)')->toolbarButtons(['bold', 'italic']),
                    ])->columns(1),
                ]),
            ]),

            Section::make('Social Media Links')->collapsible()->schema([
                TextInput::make('facebook_url')->label('Facebook URL')->url()->nullable(),
                TextInput::make('twitter_url')->label('Twitter URL')->url()->nullable(),
                TextInput::make('linkedin_url')->label('LinkedIn URL')->url()->nullable(),
            ])->columns(1),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->submit('save')
                ->color('primary'),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            $settings = Setting::find(1);
            if ($settings) {
                $settings->update($data);
            } else {
                Setting::create(array_merge(['id' => 1], $data));
            }

            Notification::make()
                ->title('Settings saved successfully!')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error saving settings!')
                ->body('Details: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
