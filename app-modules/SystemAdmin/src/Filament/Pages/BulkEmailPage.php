<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Mail;
use App\Mail\BulkEmail as BulkEmailMailable;
use App\Models\Company;
use App\Models\Visitor;
use League\Csv\Reader;

class BulkEmailPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected string $view = 'filament.pages.bulk-email';

    protected static ?string $navigationLabel = 'Bulk Email';

    protected static ?string $title = 'Bulk Email Campaign';

    protected static \UnitEnum|string|null $navigationGroup = 'Communications';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('recipient_type')
                    ->label('Send To')
                    ->options([
                        'all_companies' => 'All Companies',
                        'all_visitors' => 'All Visitors',
                        'import_csv' => 'Import from CSV',
                        'manual_emails' => 'Enter Emails Manually',
                    ])
                    ->required()
                    ->live()
                    ->default('all_companies'),

                FileUpload::make('csv_file')
                    ->label('Upload CSV File')
                    ->acceptedFileTypes(['text/csv', 'text/plain'])
                    ->helperText('CSV should have an "email" column. Optional: "name" column.')
                    ->visible(fn($get) => $get('recipient_type') === 'import_csv'),

                Textarea::make('manual_emails')
                    ->label('Email Addresses')
                    ->placeholder('Enter email addresses, one per line')
                    ->rows(5)
                    ->helperText('Enter one email per line')
                    ->visible(fn($get) => $get('recipient_type') === 'manual_emails'),

                TextInput::make('subject')
                    ->label('Email Subject')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter email subject...'),

                Textarea::make('body')
                    ->label('Email Body')
                    ->required()
                    ->rows(15)
                    ->placeholder('Enter your email message here...')
                    ->helperText('Plain text email. Line breaks will be preserved.'),
            ])
            ->statePath('data');
    }

    public function send(): void
    {
        $data = $this->form->getState();
        $recipients = $this->getRecipients($data);

        if (empty($recipients)) {
            Notification::make()
                ->title('No Recipients')
                ->body('No valid email addresses found.')
                ->warning()
                ->send();
            return;
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient['email'])->queue(
                    new BulkEmailMailable(
                        $data['subject'],
                        $data['body'],
                        (object) $recipient
                    )
                );
                $successCount++;
            } catch (\Exception $e) {
                $failCount++;
                \Log::error('Bulk email failed', [
                    'email' => $recipient['email'],
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Notification::make()
            ->title('Emails Queued')
            ->body("{$successCount} email(s) queued for sending. {$failCount} failed.")
            ->success()
            ->send();

        $this->form->fill();
    }

    protected function getRecipients(array $data): array
    {
        $recipients = [];

        switch ($data['recipient_type']) {
            case 'all_companies':
                $companies = Company::whereNotNull('email')->get();
                foreach ($companies as $company) {
                    $recipients[] = [
                        'email' => $company->email ?? null,
                        'name' => $company->name,
                    ];
                }
                break;

            case 'all_visitors':
                $visitors = Visitor::whereNotNull('email')->get();
                foreach ($visitors as $visitor) {
                    $recipients[] = [
                        'email' => $visitor->email,
                        'name' => $visitor->name,
                    ];
                }
                break;

            case 'import_csv':
                if (!empty($data['csv_file'])) {
                    $recipients = $this->parseCSV($data['csv_file']);
                }
                break;

            case 'manual_emails':
                if (!empty($data['manual_emails'])) {
                    $emails = explode("\n", $data['manual_emails']);
                    foreach ($emails as $email) {
                        $email = trim($email);
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $recipients[] = [
                                'email' => $email,
                                'name' => $email,
                            ];
                        }
                    }
                }
                break;
        }

        return array_filter($recipients, fn($r) => !empty($r['email']));
    }

    protected function parseCSV(string $filePath): array
    {
        $recipients = [];

        try {
            $csv = Reader::createFromPath(storage_path('app/' . $filePath), 'r');
            $csv->setHeaderOffset(0);

            foreach ($csv as $record) {
                if (!empty($record['email']) && filter_var($record['email'], FILTER_VALIDATE_EMAIL)) {
                    $recipients[] = [
                        'email' => $record['email'],
                        'name' => $record['name'] ?? $record['email'],
                    ];
                }
            }
        } catch (\Exception $e) {
            \Log::error('CSV parsing failed', ['error' => $e->getMessage()]);
        }

        return $recipients;
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('send')
                ->label('Send Emails')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Send Bulk Email?')
                ->modalDescription('This will send emails to all selected recipients.')
                ->action('send'),
        ];
    }
}
