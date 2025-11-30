<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Services\WhatsAppService;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class BulkWhatsAppAction
{
    public static function make(): BulkAction
    {
        return BulkAction::make('sendBulkWhatsApp')
            ->label('Send WhatsApp')
            ->icon('heroicon-o-chat-bubble-left-right')
            ->color('success')
            ->form([
                \Filament\Forms\Components\Radio::make('message_type')
                    ->label('Message Type')
                    ->options([
                        'text' => 'Free Text (24h Window Only)',
                        'template' => 'Message Template (Approved by Meta)',
                    ])
                    ->default('text')
                    ->live()
                    ->required(),

                \Filament\Forms\Components\Section::make('Template Details')
                    ->visible(fn($get) => $get('message_type') === 'template')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('template_name')
                            ->label('Template Name')
                            ->placeholder('hello_world')
                            ->helperText('The exact name of the approved template in Meta Business Manager.')
                            ->required(fn($get) => $get('message_type') === 'template'),

                        \Filament\Forms\Components\Repeater::make('template_params')
                            ->label('Body Parameters')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('value')
                                    ->label('Parameter Value')
                                    ->placeholder('e.g., John Doe')
                                    ->required(),
                            ])
                            ->addActionLabel('Add Parameter')
                            ->helperText('Add parameters in order {{1}}, {{2}}, etc.'),
                    ]),

                Textarea::make('message')
                    ->label('WhatsApp Message')
                    ->required(fn($get) => $get('message_type') === 'text')
                    ->rows(8)
                    ->placeholder('Enter your WhatsApp message here...')
                    ->helperText('Plain text message. WARNING: Only works if the user messaged you in the last 24 hours.')
                    ->visible(fn($get) => $get('message_type') === 'text'),
            ])
            ->action(function (Collection $records, array $data): void {
                $count = 0;
                $noPhoneCount = 0;

                $isTemplate = $data['message_type'] === 'template';
                $templateName = $data['template_name'] ?? null;

                // Extract values from repeater
                $templateParams = [];
                if ($isTemplate && !empty($data['template_params'])) {
                    foreach ($data['template_params'] as $param) {
                        $templateParams[] = [
                            'type' => 'text',
                            'text' => $param['value'],
                        ];
                    }
                }

                foreach ($records as $record) {
                    // Get phone number from record
                    $phone = $record->phone ?? $record->company?->phone ?? null;

                    if (empty($phone)) {
                        $noPhoneCount++;
                        continue;
                    }

                    // Dispatch job to queue
                    \App\Jobs\SendWhatsAppMessage::dispatch(
                        $phone,
                        $data['message'] ?? '',
                        $isTemplate,
                        $templateName,
                        $templateParams
                    );

                    $count++;
                }

                // Show notification
                $message = "{$count} WhatsApp message(s) queued for sending.";
                if ($noPhoneCount > 0) {
                    $message .= " {$noPhoneCount} record(s) skipped (no phone number).";
                }

                Notification::make()
                    ->title('Bulk WhatsApp Queued')
                    ->body($message)
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion()
            ->requiresConfirmation()
            ->modalHeading('Send Bulk WhatsApp')
            ->modalDescription('Send a WhatsApp message to all selected records with phone numbers.')
            ->modalSubmitActionLabel('Queue Messages');
    }
}
