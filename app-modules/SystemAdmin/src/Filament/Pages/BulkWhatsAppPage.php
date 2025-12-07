<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Pages;

use App\Jobs\SendWhatsAppMessage;
use App\Models\Company;
use App\Models\Event;
use App\Models\Participation;
use App\Models\Visitor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use League\Csv\Reader;

class BulkWhatsAppPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected string $view = 'filament.pages.bulk-whatsapp';

    protected static ?string $navigationLabel = 'Bulk WhatsApp';

    protected static ?string $title = 'Bulk WhatsApp Campaign';

    protected static \UnitEnum|string|null $navigationGroup = 'Communications';

    protected static ?int $navigationGroupSort = 10;

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'recipient_type' => 'all_companies',
            'message_type' => 'text',
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
                        'all_companies' => '📦 All Companies with Phone',
                        'all_visitors' => '👥 All Visitors with Phone',
                        'event_participants' => '🎪 Event Participants',
                        'import_csv' => '📄 Import from CSV',
                        'manual_phones' => '✏️ Enter Phone Numbers',
                    ])
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn() => $this->updateRecipientCount())
                    ->default('all_companies'),

                // Event Selection (for event participants)
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
                    ->helperText('CSV should have a "phone" column. Optional: "name" column.')
                    ->visible(fn($get) => $get('recipient_type') === 'import_csv'),

                // Manual Phone Entry
                Textarea::make('manual_phones')
                    ->label('Phone Numbers')
                    ->placeholder("Enter phone numbers, one per line:\n+971501234567\n+971509876543")
                    ->rows(5)
                    ->helperText('Include country code (e.g., +971). One number per line.')
                    ->visible(fn($get) => $get('recipient_type') === 'manual_phones'),

                // Recipient Count Preview
                Placeholder::make('recipient_preview')
                    ->label('')
                    ->content(fn() => $this->getRecipientPreviewContent())
                    ->columnSpanFull(),

                // Message Type
                Radio::make('message_type')
                    ->label('Message Type')
                    ->options([
                        'text' => '💬 Free Text Message (24-hour window)',
                        'template' => '📋 Message Template (Business initiated)',
                    ])
                    ->descriptions([
                        'text' => 'Only works if recipient messaged you in the last 24 hours',
                        'template' => 'Use pre-approved Meta templates for first-time messages',
                    ])
                    ->default('text')
                    ->live()
                    ->required(),

                // Template Fields
                TextInput::make('template_name')
                    ->label('Template Name')
                    ->placeholder('hello_world')
                    ->helperText('The exact template name from Meta Business Manager')
                    ->required(fn($get) => $get('message_type') === 'template')
                    ->visible(fn($get) => $get('message_type') === 'template'),

                Select::make('template_language')
                    ->label('Template Language')
                    ->options([
                        'en' => 'English',
                        'ar' => 'Arabic',
                        'en_US' => 'English (US)',
                        'en_GB' => 'English (UK)',
                    ])
                    ->default('en')
                    ->visible(fn($get) => $get('message_type') === 'template'),

                Repeater::make('template_params')
                    ->label('Template Parameters')
                    ->schema([
                        TextInput::make('value')
                            ->label('Parameter Value')
                            ->placeholder('e.g., John Doe')
                            ->required(),
                    ])
                    ->addActionLabel('+ Add Parameter')
                    ->helperText('Add values for {{1}}, {{2}}, etc. in order')
                    ->visible(fn($get) => $get('message_type') === 'template')
                    ->collapsible(),

                // Free Text Message
                Textarea::make('message')
                    ->label('Message Content')
                    ->required(fn($get) => $get('message_type') === 'text')
                    ->rows(8)
                    ->placeholder("Type your message here...\n\nYou can use:\n• Emojis 👋\n• Line breaks\n• Plain text only")
                    ->helperText('⚠️ Free text only works within 24-hour conversation window')
                    ->visible(fn($get) => $get('message_type') === 'text'),
            ])
            ->statePath('data');
    }

    public function updateRecipientCount(): void
    {
        // Trigger re-render for placeholder
    }

    protected function getRecipientPreviewContent(): string
    {
        $data = $this->data ?? [];
        $type = $data['recipient_type'] ?? 'all_companies';
        $count = 0;
        $label = '';

        switch ($type) {
            case 'all_companies':
                $count = Company::whereNotNull('phone')->where('phone', '!=', '')->count();
                $label = 'companies with phone numbers';
                break;
            case 'all_visitors':
                $count = Visitor::whereNotNull('phone')->where('phone', '!=', '')->count();
                $label = 'visitors with phone numbers';
                break;
            case 'event_participants':
                $eventId = $data['event_id'] ?? null;
                if ($eventId) {
                    $count = Participation::where('event_id', $eventId)
                        ->whereHas('company', fn($q) => $q->whereNotNull('phone')->where('phone', '!=', ''))
                        ->count();
                    $label = 'participants with phone numbers';
                } else {
                    return '<div class="text-amber-600 font-medium">⚠️ Please select an event</div>';
                }
                break;
            case 'manual_phones':
                $phones = $data['manual_phones'] ?? '';
                $count = count(array_filter(explode("\n", $phones), fn($p) => !empty(trim($p))));
                $label = 'phone numbers entered';
                break;
            case 'import_csv':
                return '<div class="text-blue-600 font-medium">📄 Upload a CSV file to see recipient count</div>';
        }

        if ($count === 0) {
            return '<div class="text-red-600 font-medium">❌ No recipients found</div>';
        }

        return "<div class='text-green-600 font-medium text-lg'>✅ {$count} {$label} will receive this message</div>";
    }

    public function send(): void
    {
        try {
            $data = $this->form->getState();
            $recipients = $this->getRecipients($data);

            if (empty($recipients)) {
                Notification::make()
                    ->title('No Recipients')
                    ->body('No valid phone numbers found.')
                    ->warning()
                    ->send();
                return;
            }

            $count = count($recipients);
            $isTemplate = $data['message_type'] === 'template';
            $templateName = $data['template_name'] ?? null;
            $templateLanguage = $data['template_language'] ?? 'en';

            // Build template parameters
            $templateParams = [];
            if ($isTemplate && !empty($data['template_params'])) {
                foreach ($data['template_params'] as $param) {
                    $templateParams[] = [
                        'type' => 'text',
                        'text' => $param['value'],
                    ];
                }
            }

            // Queue messages
            foreach ($recipients as $recipient) {
                SendWhatsAppMessage::dispatch(
                    $recipient['phone'],
                    $data['message'] ?? '',
                    $isTemplate,
                    $templateName,
                    $templateParams
                );
            }

            Notification::make()
                ->title('Messages Queued!')
                ->body("{$count} WhatsApp message(s) have been queued for sending.")
                ->success()
                ->send();

            $this->form->fill([
                'recipient_type' => 'all_companies',
                'message_type' => 'text',
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
                $companies = Company::whereNotNull('phone')
                    ->where('phone', '!=', '')
                    ->get(['phone', 'name']);
                foreach ($companies as $company) {
                    $recipients[] = [
                        'phone' => $this->formatPhone($company->phone),
                        'name' => $company->name,
                    ];
                }
                break;

            case 'all_visitors':
                $visitors = Visitor::whereNotNull('phone')
                    ->where('phone', '!=', '')
                    ->get(['phone', 'name']);
                foreach ($visitors as $visitor) {
                    $recipients[] = [
                        'phone' => $this->formatPhone($visitor->phone),
                        'name' => $visitor->name,
                    ];
                }
                break;

            case 'event_participants':
                $eventId = $data['event_id'] ?? null;
                if ($eventId) {
                    $participations = Participation::where('event_id', $eventId)
                        ->with('company')
                        ->whereHas('company', fn($q) => $q->whereNotNull('phone')->where('phone', '!=', ''))
                        ->get();
                    foreach ($participations as $participation) {
                        $recipients[] = [
                            'phone' => $this->formatPhone($participation->company->phone),
                            'name' => $participation->company->name,
                        ];
                    }
                }
                break;

            case 'import_csv':
                if (!empty($data['csv_file'])) {
                    $recipients = $this->parseCSV($data['csv_file'], 'phone');
                }
                break;

            case 'manual_phones':
                if (!empty($data['manual_phones'])) {
                    $phones = explode("\n", $data['manual_phones']);
                    foreach ($phones as $phone) {
                        $phone = trim($phone);
                        if (!empty($phone)) {
                            $recipients[] = [
                                'phone' => $this->formatPhone($phone),
                                'name' => $phone,
                            ];
                        }
                    }
                }
                break;
        }

        return array_filter($recipients, fn($r) => !empty($r['phone']));
    }

    protected function formatPhone(string $phone): string
    {
        // Remove spaces and special characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // Ensure starts with +
        if (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }

        return $phone;
    }

    protected function parseCSV(string $filePath, string $columnName): array
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
                if (!empty($record[$columnName])) {
                    $recipients[] = [
                        'phone' => $this->formatPhone($record[$columnName]),
                        'name' => $record['name'] ?? $record[$columnName],
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
                ->label('📤 Send WhatsApp Messages')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->size('lg')
                ->requiresConfirmation()
                ->modalHeading('Send Bulk WhatsApp Messages?')
                ->modalDescription('Messages will be queued and sent via WhatsApp Cloud API. Make sure you have configured your API credentials.')
                ->modalSubmitActionLabel('Yes, Send Now')
                ->action('send'),
        ];
    }
}
