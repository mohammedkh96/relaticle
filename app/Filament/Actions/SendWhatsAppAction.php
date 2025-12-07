<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Services\WhatsAppService;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class SendWhatsAppAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'sendWhatsApp';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Send WhatsApp')
            ->icon('heroicon-o-chat-bubble-left-right')
            ->color('success')
            ->form([
                Textarea::make('message')
                    ->label('Message')
                    ->required()
                    ->rows(5)
                    ->placeholder('Enter your message here...'),
            ])
            ->action(function (array $data, $record): void {
                try {
                    $whatsappService = app(WhatsAppService::class);

                    // Get phone number from record
                    $phone = $record->phone ?? null;

                    if (empty($phone)) {
                        Notification::make()
                            ->title('No phone number')
                            ->body('This record does not have a phone number.')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Send message
                    $result = $whatsappService->sendMessage($phone, $data['message']);

                    Notification::make()
                        ->title('Message sent')
                        ->body('WhatsApp message sent successfully.')
                        ->success()
                        ->send();

                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Failed to send')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
}
