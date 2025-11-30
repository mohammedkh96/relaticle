<?php

namespace Relaticle\SystemAdmin\Filament\Pages;

use App\Settings\CommunicationSettings;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class CommunicationSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.pages.communication-settings-page';

    protected static ?string $navigationLabel = 'Communication Settings';

    protected static ?string $title = 'Communication Settings';

    protected static \UnitEnum|string|null $navigationGroup = 'Communications';

    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public function mount(CommunicationSettings $settings): void
    {
        $this->form->fill([
            'mail_mailer' => $settings->mail_mailer ?? 'smtp',
            'mail_host' => $settings->mail_host,
            'mail_port' => $settings->mail_port,
            'mail_username' => $settings->mail_username,
            'mail_password' => $settings->mail_password,
            'mail_encryption' => $settings->mail_encryption,
            'mail_from_address' => $settings->mail_from_address,
            'mail_from_name' => $settings->mail_from_name,
            'whatsapp_api_url' => $settings->whatsapp_api_url ?? 'https://graph.facebook.com/v21.0',
            'whatsapp_api_token' => $settings->whatsapp_api_token,
            'whatsapp_phone_number_id' => $settings->whatsapp_phone_number_id,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('mail_mailer')
                    ->label('Email Mailer')
                    ->default('smtp')
                    ->required(),
                TextInput::make('mail_host')
                    ->label('Email Host')
                    ->placeholder('smtp.gmail.com')
                    ->required(),
                TextInput::make('mail_port')
                    ->label('Email Port')
                    ->numeric()
                    ->default(587)
                    ->required(),
                TextInput::make('mail_username')
                    ->label('Email Username')
                    ->required(),
                TextInput::make('mail_password')
                    ->label('Email Password')
                    ->password()
                    ->revealable()
                    ->required(),
                TextInput::make('mail_encryption')
                    ->label('Email Encryption')
                    ->default('tls'),
                TextInput::make('mail_from_address')
                    ->label('From Email Address')
                    ->email()
                    ->required(),
                TextInput::make('mail_from_name')
                    ->label('From Name')
                    ->default(config('app.name')),
                TextInput::make('whatsapp_api_url')
                    ->label('WhatsApp API URL')
                    ->default('https://graph.facebook.com/v21.0')
                    ->required(),
                TextInput::make('whatsapp_phone_number_id')
                    ->label('WhatsApp Phone Number ID')
                    ->required(),
                TextInput::make('whatsapp_api_token')
                    ->label('WhatsApp Access Token')
                    ->password()
                    ->revealable()
                    ->required(),
            ])
            ->columns(2)
            ->statePath('data');
    }

    public function save(CommunicationSettings $settings): void
    {
        $data = $this->form->getState();

        $settings->mail_mailer = $data['mail_mailer'];
        $settings->mail_host = $data['mail_host'];
        $settings->mail_port = (int) $data['mail_port'];
        $settings->mail_username = $data['mail_username'];
        $settings->mail_password = $data['mail_password'];
        $settings->mail_encryption = $data['mail_encryption'];
        $settings->mail_from_address = $data['mail_from_address'];
        $settings->mail_from_name = $data['mail_from_name'];

        $settings->whatsapp_api_url = $data['whatsapp_api_url'];
        $settings->whatsapp_api_token = $data['whatsapp_api_token'];
        $settings->whatsapp_phone_number_id = $data['whatsapp_phone_number_id'];

        $settings->save();

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
