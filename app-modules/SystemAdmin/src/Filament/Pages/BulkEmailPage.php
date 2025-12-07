<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Pages;

use App\Mail\BulkEmail as BulkEmailMailable;
use App\Models\Company;
use App\Models\Event;
use App\Models\Participation;
use App\Models\Visitor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Mail;
use League\Csv\Reader;

class BulkEmailPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected string $view = 'filament.pages.bulk-email';

    protected static ?string $navigationLabel = 'Bulk Email';

    protected static ?string $title = 'Bulk Email Campaign';

    protected static \UnitEnum|string|null $navigationGroup = 'Communications';

    protected static ?int $navigationGroupSort = 10;

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'recipient_type' => 'all_companies',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Recipient Selection
                Select::make('recipient_type')
                    ->label('Send To')
                    ->options([
                        'all_companies' => '📦 All Companies with Email',
                        'all_visitors' => '👥 All Visitors with Email',
                        'event_participants' => '🎪 Event Participants',
                        'import_csv' => '📄 Import from CSV',
                        'manual_emails' => '✏️ Enter Email Addresses',
                    ])
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn() => $this->updateRecipientCount())
                    ->default('all_companies'),

                // Event Selection
                Select::make('event_id')
                    ->label('Select Event')
                    ->options(Event::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn() => $this->updateRecipientCount())
                    ->visible(fn($get) => $get('recipient_type') === 'event_participants'),

                // CSV Upload
                FileUpload::make('csv_file')
                    ->label('Upload CSV File')
                    ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])
                    ->helperText('CSV should have an "email" column. Optional: "name" column.')
                    ->visible(fn($get) => $get('recipient_type') === 'import_csv'),

                // Manual Email Entry
                Textarea::make('manual_emails')
                    ->label('Email Addresses')
                    ->placeholder("Enter email addresses, one per line:\njohn@example.com\njane@company.com")
                    ->rows(5)
                    ->helperText('One email per line')
                    ->visible(fn($get) => $get('recipient_type') === 'manual_emails'),

                // Recipient Count Preview
                Placeholder::make('recipient_preview')
                    ->label('')
                    ->content(fn() => $this->getRecipientPreviewContent())
                    ->columnSpanFull(),

                // Email Subject
                TextInput::make('subject')
                    ->label('Email Subject')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter your email subject...')
                    ->prefixIcon('heroicon-o-envelope'),

                // Email Body - Using Textarea with markdown support
                Textarea::make('body')
                    ->label('Email Body')
                    ->required()
                    ->rows(12)
                    ->placeholder("Write your email here...\n\nYou can use:\n• Line breaks\n• Plain text formatting\n\nBest regards,\nYour Team")
                    ->helperText('Plain text email. Line breaks will be preserved. Use {name} for personalization.'),
            ])
            ->statePath('data');
    }

    public function updateRecipientCount(): void
    {
        // Trigger re-render
    }

    protected function getRecipientPreviewContent(): string
    {
        $data = $this->data ?? [];
        $type = $data['recipient_type'] ?? 'all_companies';
        $count = 0;
        $label = '';

        switch ($type) {
            case 'all_companies':
                $count = Company::whereNotNull('email')->where('email', '!=', '')->count();
                $label = 'companies with email addresses';
                break;
            case 'all_visitors':
                $count = Visitor::whereNotNull('email')->where('email', '!=', '')->count();
                $label = 'visitors with email addresses';
                break;
            case 'event_participants':
                $eventId = $data['event_id'] ?? null;
                if ($eventId) {
                    $count = Participation::where('event_id', $eventId)
                        ->whereHas('company', fn($q) => $q->whereNotNull('email')->where('email', '!=', ''))
                        ->count();
                    $label = 'participants with email addresses';
                } else {
                    return '<div class="text-amber-600 font-medium">⚠️ Please select an event</div>';
                }
                break;
            case 'manual_emails':
                $emails = $data['manual_emails'] ?? '';
                $count = count(array_filter(explode("\n", $emails), fn($e) => filter_var(trim($e), FILTER_VALIDATE_EMAIL)));
                $label = 'valid email addresses entered';
                break;
            case 'import_csv':
                return '<div class="text-blue-600 font-medium">📄 Upload a CSV file to see recipient count</div>';
        }

        if ($count === 0) {
            return '<div class="text-red-600 font-medium">❌ No recipients found</div>';
        }

        return "<div class='text-green-600 font-medium text-lg'>✅ {$count} {$label} will receive this email</div>";
    }

    public function send(): void
    {
        try {
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
                    // Personalize body with recipient name
                    $personalizedBody = str_replace('{name}', $recipient['name'], $data['body']);

                    Mail::to($recipient['email'])->queue(
                        new BulkEmailMailable(
                            $data['subject'],
                            $personalizedBody,
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
                ->title('Emails Queued!')
                ->body("{$successCount} email(s) queued for sending." . ($failCount > 0 ? " {$failCount} failed." : ''))
                ->success()
                ->send();

            $this->form->fill([
                'recipient_type' => 'all_companies',
            ]);

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getRecipients(array $data): array
    {
        $recipients = [];

        switch ($data['recipient_type']) {
            case 'all_companies':
                $companies = Company::whereNotNull('email')
                    ->where('email', '!=', '')
                    ->get(['email', 'name']);
                foreach ($companies as $company) {
                    $recipients[] = [
                        'email' => $company->email,
                        'name' => $company->name,
                    ];
                }
                break;

            case 'all_visitors':
                $visitors = Visitor::whereNotNull('email')
                    ->where('email', '!=', '')
                    ->get(['email', 'name']);
                foreach ($visitors as $visitor) {
                    $recipients[] = [
                        'email' => $visitor->email,
                        'name' => $visitor->name,
                    ];
                }
                break;

            case 'event_participants':
                $eventId = $data['event_id'] ?? null;
                if ($eventId) {
                    $participations = Participation::where('event_id', $eventId)
                        ->with('company')
                        ->whereHas('company', fn($q) => $q->whereNotNull('email')->where('email', '!=', ''))
                        ->get();
                    foreach ($participations as $participation) {
                        $recipients[] = [
                            'email' => $participation->company->email,
                            'name' => $participation->company->name,
                        ];
                    }
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

        return array_filter($recipients, fn($r) => !empty($r['email']) && filter_var($r['email'], FILTER_VALIDATE_EMAIL));
    }

    protected function parseCSV(string $filePath): array
    {
        $recipients = [];

        try {
            $fullPath = storage_path('app/public/' . $filePath);
            if (!file_exists($fullPath)) {
                $fullPath = storage_path('app/' . $filePath);
            }

            $csv = Reader::createFromPath($fullPath, 'r');
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
                ->label('📤 Send Emails')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->size('lg')
                ->requiresConfirmation()
                ->modalHeading('Send Bulk Email?')
                ->modalDescription('Emails will be queued and sent in the background. Make sure your SMTP settings are configured.')
                ->modalSubmitActionLabel('Yes, Send Now')
                ->action('send'),
        ];
    }
}
