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
                Textarea::make('message')
                    ->label('WhatsApp Message')
                    ->required()
                    ->rows(8)
                    ->placeholder('Enter your WhatsApp message here...')
                    ->helperText('This message will be sent to all selected recipients with phone numbers.'),
            ])
            ->action(function (Collection $records, array $data): void {
                $whatsappService = app(WhatsAppService::class);
                $successCount = 0;
                $failCount = 0;
                $noPhoneCount = 0;

                foreach ($records as $record) {
                    // Get phone number from record
                    $phone = $record->phone ?? $record->company?->phone ?? null;

                    if (empty($phone)) {
                        $noPhoneCount++;
                        continue;
                    }

                    try {
                        $result = $whatsappService->sendMessage($phone, $data['message']);

                        if ($result['success']) {
                            $successCount++;
                        } else {
                            $failCount++;
                            \Log::warning('Bulk WhatsApp failed', [
                                'phone' => $phone,
                                'message' => $result['message'] ?? 'Unknown error',
                            ]);
                        }
                    } catch (\Exception $e) {
                        $failCount++;
                        \Log::error('Bulk WhatsApp exception', [
                            'phone' => $phone,
                            'error' => $e->getMessage(),
                        ]);
                    }

                    // Small delay to avoid rate limiting
                    usleep(100000); // 0.1 second delay
                }

                // Show notification
                $message = "{$successCount} WhatsApp message(s) sent successfully.";
                if ($noPhoneCount > 0) {
                    $message .= " {$noPhoneCount} record(s) skipped (no phone number).";
                }
                if ($failCount > 0) {
                    $message .= " {$failCount} failed.";
                }

                $notificationType = $failCount > 0 ? 'warning' : 'success';

                Notification::make()
                            ->title('Bulk WhatsApp Sent')
                            ->body($message)
                    ->{$notificationType}()
                        ->send();
            })
            ->deselectRecordsAfterCompletion()
            ->requiresConfirmation()
            ->modalHeading('Send Bulk WhatsApp')
            ->modalDescription('Send a WhatsApp message to all selected records with phone numbers.')
            ->modalSubmitActionLabel('Send Messages');
    }
}
