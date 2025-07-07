<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Filament\Resources\LeadResource\RelationManagers;
use App\Models\Lead; // Corrected to Lead model
use Filament\Forms\Form;
use Filament\Forms\Components\Textarea; // For message
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select; // For enquiry_type
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Actions\Action; // For custom actions like marking read
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon; // For date formatting if needed

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox'; // Icon for leads/inquiries
    protected static ?string $navigationLabel = 'Customer Leads';
    protected static ?string $navigationGroup = 'Sales & Inquiries'; // A new group

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->disabled(), // Leads are read-only in form generally, enable for edit if needed
                TextInput::make('email')
                    ->required()
                    ->email()
                    ->maxLength(255)
                    ->disabled(),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(255)
                    ->disabled(),
                TextInput::make('subject')
                    ->maxLength(255)
                    ->disabled(),
                Select::make('enquiry_type') // New dropdown for enquiry type
                    ->options([
                        'general' => 'General Inquiry',
                        'product_inquiry' => 'Product Inquiry',
                        'partner_inquiry' => 'Partner Inquiry',
                        'career_inquiry' => 'Career Inquiry',
                        // Add more types as needed
                    ])
                    ->required()
                    ->disabled(),
                Textarea::make('message')
                    ->required()
                    ->columnSpanFull()
                    ->rows(5)
                    ->disabled(),
                Toggle::make('is_read')
                    ->label('Mark as Read')
                    ->helperText('Toggle to mark this lead as processed.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold') // Make name prominent
                    ->color(fn (Lead $record): string => $record->is_read ? 'gray' : 'primary'), // Color based on read status
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('subject')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('enquiry_type') // Display enquiry type
                    ->searchable()
                    ->sortable()
                    ->badge() // Display as a badge
                    ->color(fn (string $state): string => match ($state) {
                        'general' => 'info',
                        'product_inquiry' => 'success',
                        'partner_inquiry' => 'warning',
                        'career_inquiry' => 'secondary',
                        default => 'gray',
                    }),
                ToggleColumn::make('is_read')
                    ->label('Read'),
                TextColumn::make('created_at')
                    ->dateTime('d M Y H:i') // Format date for display
                    ->sortable()
                    ->label('Received At'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_read')
                    ->label('Read Status')
                    ->boolean()
                    ->trueLabel('Read')
                    ->falseLabel('Unread')
                    ->default(false), // Default filter to show unread leads
                Tables\Filters\SelectFilter::make('enquiry_type')
                    ->label('Enquiry Type')
                    ->options([
                        'general' => 'General Inquiry',
                        'product_inquiry' => 'Product Inquiry',
                        'partner_inquiry' => 'Partner Inquiry',
                        'career_inquiry' => 'Career Inquiry',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(), // Provides a read-only view of the form
                Tables\Actions\EditAction::make(), // Allows editing (like changing is_read status)
                // Custom action to quickly mark as read
                Action::make('markAsRead')
                    ->label('Mark as Read')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Lead $record): bool => !$record->is_read) // Only show if unread
                    ->action(function (Lead $record) {
                        $record->is_read = true;
                        $record->save();
                        \Filament\Notifications\Notification::make()
                            ->title('Lead marked as read!')
                            ->success()
                            ->send();
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    // Bulk action to mark multiple as read
                    Tables\Actions\BulkAction::make('markAllAsRead')
                        ->label('Mark All as Read')
                        ->icon('heroicon-o-check-double-arrow-right')
                        ->color('success')
                        ->action(function (Tables\Actions\BulkAction $action, \Illuminate\Support\Collection $records) {
                            $records->each->update(['is_read' => true]);
                            \Filament\Notifications\Notification::make()
                                ->title('Selected leads marked as read!')
                                ->success()
                                ->send();
                        })
                ]),
            ])
            ->defaultSort('created_at', 'desc'); // Sort by newest leads first
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
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'), // Create action will not be used, but page exists
            'edit' => Pages\EditLead::route('/{record}/edit'),
            'view' => Pages\ViewLead::route('/{record}'), // Added view page for read-only access
        ];
    }

    // Since leads are created by the public form, these are not needed for Filament resource
    // public static function mutateFormDataBeforeCreate(array $data): array { return $data; }
    // public static function mutateFormDataBeforeSave(array $data): array { return $data; }
}