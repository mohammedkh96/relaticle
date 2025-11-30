<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Pages;

use App\Services\WhatsAppService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use App\Models\Company;
use App\Models\Visitor;
use League\Csv\Reader;

class BulkWhatsAppPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected string $view = 'filament.pages.bulk-whatsapp';

    protected static ?string $navigationLabel = 'Bulk WhatsApp';

    protected static ?string $title = 'Bulk WhatsApp Campaign';

    protected static \UnitEnum|string|null $navigationGroup = 'Communications';

    protected static ?int $navigationSort = 2;

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
                        'manual_phones' => 'Enter Phone Numbers Manually',
                    ])
                    ->required()
                    ->live()
                    ->default('all_companies'),

                FileUpload::make('csv_file')
                    ->label('Upload CSV File')
                    ->acceptedFileTypes(['text/csv', 'text/plain'])
                    ->helperText('CSV should have a "phone" column. Optional: "name" column.')
                    ->visible(fn($get) => $get('recipient_type') === 'import_csv'),

                Textarea::make('manual_phones')
                    ->label('Phone Numbers')
                    ->placeholder('Enter phone numbers, one per line (with country code, e.g., +1234567890)')
                    ->rows(5)
                    ->helperText('Enter one phone number per line. Include country code (+)')
                    ->visible(fn($get) => $get('recipient_type') === 'manual_phones'),

                Textarea::make('message')
                    ->label('WhatsApp Message')
                    ->required()
                    ->rows(10)
                    ->placeholder('Enter your WhatsApp message here...')
                    ->helperText('Plain text message. Keep it concise for better delivery.'),
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
                ->body('No valid phone numbers found.')
                ->warning()
                ->send();
            return;
        }

        $whatsappService = app(WhatsAppService::class);
        $successCount = 0;
        $failCount = 0;

        foreach ($recipients as $recipient) {
            try {
                $result = $whatsappService->sendMessage($recipient['phone'], $data['message']);

                if ($result['success']) {
                    $successCount++;
                } else {
                    $failCount++;
                    \Log::warning('Bulk WhatsApp failed', [
                        'phone' => $recipient['phone'],
                        'message' => $result['message'] ?? 'Unknown error',
                    ]);
                }

                // Rate limiting - 0.2 second delay
                usleep(200000);
            } catch (\Exception $e) {
                $failCount++;
                \Log::error('Bulk WhatsApp exception', [
                    'phone' => $recipient['phone'],
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $notificationType = $failCount > 0 ? 'warning' : 'success';

        Notification::make()
                    ->title('WhatsApp Messages Sent')
                    ->body("{$successCount} message(s) sent successfully. {$failCount} failed.")
            ->{$notificationType}()
                ->send();

        $this->form->fill();
    }

    protected function getRecipients(array $data): array
    {
        $recipients = [];

        switch ($data['recipient_type']) {
            case 'all_companies':
                $companies = Company::whereNotNull('phone')->get();
                foreach ($companies as $company) {
                    $recipients[] = [
                        'phone' => $company->phone,
                        'name' => $company->name,
                    ];
                }
                break;

            case 'all_visitors':
                $visitors = Visitor::whereNotNull('phone')->get();
                foreach ($visitors as $visitor) {
                    $recipients[] = [
                        'phone' => $visitor->phone,
                        'name' => $visitor->name,
                    ];
                }
                break;

            case 'import_csv':
                if (!empty($data['csv_file'])) {
                    $recipients = $this->parseCSV($data['csv_file']);
                }
                break;

            case 'manual_phones':
                if (!empty($data['manual_phones'])) {
                    $phones = explode("\n", $data['manual_phones']);
                    foreach ($phones as $phone) {
                        $phone = trim($phone);
                        if (!empty($phone) && str_starts_with($phone, '+')) {
                            $recipients[] = [
                                'phone' => $phone,
                                'name' => $phone,
                            ];
                        }
                    }
                }
                break;
        }

        return array_filter($recipients, fn($r) => !empty($r['phone']));
    }

    protected function parseCSV(string $filePath): array
    {
        $recipients = [];

        try {
            $csv = Reader::createFromPath(storage_path('app/' . $filePath), 'r');
            $csv->setHeaderOffset(0);

            foreach ($csv as $record) {
                if (!empty($record['phone'])) {
                    $recipients[] = [
                        'phone' => $record['phone'],
                        'name' => $record['name'] ?? $record['phone'],
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
                ->label('Send Messages')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Send Bulk WhatsApp?')
                ->modalDescription('This will send WhatsApp messages to all selected recipients.')
                ->action('send'),
        ];
    }
}
