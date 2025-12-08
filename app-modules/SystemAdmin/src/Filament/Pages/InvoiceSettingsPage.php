<?php

namespace Relaticle\SystemAdmin\Filament\Pages;

use App\Settings\InvoiceSettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class InvoiceSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected string $view = 'filament.pages.invoice-settings-page';

    protected static ?string $navigationLabel = 'Invoice Settings';

    protected static ?string $title = 'Invoice Settings';

    protected static \UnitEnum|string|null $navigationGroup = 'Financials';

    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public function mount(InvoiceSettings $settings): void
    {
        $this->form->fill([
            'company_name' => $settings->company_name ?? config('app.name'),
            'company_address' => $settings->company_address,
            'company_phone' => $settings->company_phone,
            'company_logo' => $settings->company_logo,
            'invoice_note' => $settings->invoice_note,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Company Information')
                    ->description('These details will appear on the invoices.')
                    ->schema([
                        TextInput::make('company_name')
                            ->label('Company Name')
                            ->required(),
                        Textarea::make('company_address')
                            ->label('Address')
                            ->rows(3),
                        TextInput::make('company_phone')
                            ->label('Phone Number')
                            ->tel(),
                        FileUpload::make('company_logo')
                            ->label('Logo')
                            ->image()
                            ->directory('settings')
                            ->visibility('public'),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Invoice Defaults')
                    ->schema([
                        Textarea::make('invoice_note')
                            ->label('Default Footer Note')
                            ->helperText('This note will appear at the bottom of every invoice.')
                            ->rows(3),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(InvoiceSettings $settings): void
    {
        $data = $this->form->getState();

        $settings->company_name = $data['company_name'];
        $settings->company_address = $data['company_address'];
        $settings->company_phone = $data['company_phone'];
        $settings->company_logo = $data['company_logo']; // FileUpload returns path string or array? Usually string for single
        $settings->invoice_note = $data['invoice_note'];

        $settings->save();

        Notification::make()
            ->title('Invoice settings saved successfully')
            ->success()
            ->send();
    }
}
