<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;
use App\Mail\BulkEmail as BulkEmailMailable;

class BulkEmailAction
{
    public static function make(): BulkAction
    {
        return BulkAction::make('sendBulkEmail')
            ->label('Send Email')
            ->icon('heroicon-o-envelope')
            ->color('info')
            ->form([
                TextInput::make('subject')
                    ->label('Email Subject')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter email subject...'),
                Textarea::make('body')
                    ->label('Email Body')
                    ->required()
                    ->rows(10)
                    ->placeholder('Enter your message here...')
                    ->helperText('This message will be sent to all selected recipients.'),
            ])
            ->action(function (Collection $records, array $data): void {
                $successCount = 0;
                $failCount = 0;
                $noEmailCount = 0;

                foreach ($records as $record) {
                    // Check if record has email
                    if (empty($record->email)) {
                        $noEmailCount++;
                        continue;
                    }

                    try {
                        // Queue email for background processing
                        Mail::to($record->email)->queue(
                            new BulkEmailMailable(
                                $data['subject'],
                                $data['body'],
                                $record
                            )
                        );
                        $successCount++;
                    } catch (\Exception $e) {
                        $failCount++;
                        \Log::error('Bulk email failed', [
                            'email' => $record->email,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                // Show notification
                $message = "{$successCount} email(s) queued for sending.";
                if ($noEmailCount > 0) {
                    $message .= " {$noEmailCount} record(s) skipped (no email).";
                }
                if ($failCount > 0) {
                    $message .= " {$failCount} failed.";
                }

                Notification::make()
                    ->title('Bulk Email Sent')
                    ->body($message)
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion()
            ->requiresConfirmation()
            ->modalHeading('Send Bulk Email')
            ->modalDescription('Send an email to all selected records. Emails will be queued for background processing.')
            ->modalSubmitActionLabel('Send Emails');
    }
}
