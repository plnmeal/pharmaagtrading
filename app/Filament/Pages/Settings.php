<?php

namespace App\Filament\Pages;

use Filament\Actions\Action; // Import Action class
use Filament\Forms\Components\FileUpload; // Import FileUpload for images
use Filament\Forms\Components\RichEditor; // Import RichEditor for text areas
use Filament\Forms\Components\Section; // Import Section for better organization
use Filament\Forms\Components\TextInput; // Import TextInput for simple text fields
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB; // To easily get/update the single settings row
use Filament\Notifications\Notification; // For success/error messages

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog'; // Icon for the sidebar
    protected static ?string $navigationGroup = 'Site Management'; // Group in sidebar
    protected static string $view = 'filament.pages.settings'; // Blade view for this page
    protected static ?string $title = 'Global Site Settings'; // Title in Filament

    public ?array $data = []; // Holds the form data

    public function mount(): void
    {
        // Load the existing settings, or create a new empty one if it doesn't exist
        $settings = DB::table('settings')->first();

        if (!$settings) {
            // If no settings exist, create a new row with defaults
            DB::table('settings')->insert([
                'site_name' => 'PharmaAGTrading',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $settings = DB::table('settings')->first(); // Fetch it again
        }

        // Fill the form with the retrieved settings
        $this->form->fill((array) $settings);
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Homepage Content')
                ->description('Manage text and images for the main homepage sections.')
                ->schema([
                    TextInput::make('hero_title')
                        ->label('Hero Title')
                        ->placeholder('Your Gateway to Quality Pharma Solutions')
                        ->maxLength(255),
RichEditor::make('hero_subtitle')
    ->label('Hero Subtitle')
    ->placeholder('Powering the health of the Dominican Republic...')
    ->toolbarButtons(['bold', 'italic', 'strike', 'underline', 'link']), // <--- CORRECTED
                    FileUpload::make('hero_image_path')
                        ->label('Hero Background Image')
                        ->image()
                        ->directory('settings'), // Store images in 'public/uploads/settings'
                ]),

            Section::make('Call to Action (CTA) Section')
                ->description('Customize the call to action section at the bottom of the homepage.')
                ->schema([
                    TextInput::make('cta_title')
                        ->label('CTA Title')
                        ->placeholder('Build the Future of Healthcare, Together.')
                        ->maxLength(255),
RichEditor::make('cta_description')
    ->label('CTA Description')
    ->placeholder('Join our network of leading manufacturers...')
    ->toolbarButtons(['bold', 'italic', 'strike', 'underline', 'link']), // <--- CORRECTED
                    TextInput::make('cta_button_text')
                        ->label('CTA Button Text')
                        ->placeholder('Become a Partner')
                        ->maxLength(255),
                    TextInput::make('cta_button_link')
                        ->label('CTA Button Link')
                        ->placeholder('mailto:partners@yourclientdomain.com')
                        ->url(), // Add URL validation
                    FileUpload::make('cta_background_image_path')
                        ->label('CTA Background Image')
                        ->image()
                        ->directory('settings'),
                ]),

            Section::make('Contact Information')
                ->description('Update contact details displayed in the footer and contact page.')
                ->schema([
                    TextInput::make('contact_address')
                        ->label('Address')
                        ->placeholder('Av. John F. Kennedy, Santo Domingo, D.N., 10122')
                        ->maxLength(255),
                    TextInput::make('contact_email')
                        ->label('Primary Contact Email')
                        ->placeholder('info.do@PharmaAGTrading.com')
                        ->email(), // Add email validation
                    TextInput::make('contact_phone')
                        ->label('Phone Number')
                        ->placeholder('+1 (809) 123-4567')
                        ->tel(), // Add telephone validation
                ]),

            Section::make('Social Media Links')
                ->description('Links to your company\'s social media profiles.')
                ->schema([
                    TextInput::make('facebook_url')
                        ->label('Facebook URL')
                        ->url(),
                    TextInput::make('twitter_url')
                        ->label('Twitter URL')
                        ->url(),
                    TextInput::make('linkedin_url')
                        ->label('LinkedIn URL')
                        ->url(),
                ]),

            Section::make('General Site Information')
                ->description('Overall site details, used for titles and descriptions.')
                ->schema([
                    TextInput::make('site_name')
                        ->label('Site Name / Logo Text')
                        ->placeholder('PharmaAGTrading')
                        ->required() // This is a crucial field
                        ->maxLength(255),
RichEditor::make('site_description')
    ->label('Site Description (for footer / meta tag)')
    ->placeholder('Redefining pharmaceutical distribution...')
    ->toolbarButtons(['bold', 'italic', 'link']), // <--- CORRECTED
                ]),
        ];
    }

    // Define actions available on the page
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->submit('save') // Binds to the save method
                ->color('primary'),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            // Find the existing settings record or create one if it doesn't exist
            $settings = DB::table('settings')->first();

            if ($settings) {
                DB::table('settings')->update($data);
            } else {
                DB::table('settings')->insert($data);
            }

            Notification::make()
                ->title('Settings saved successfully!')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error saving settings!')
                ->body($e->getMessage()) // Show actual error for debugging
                ->danger()
                ->send();
        }
    }
}