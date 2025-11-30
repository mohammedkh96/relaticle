<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CommunicationSettings extends Settings
{
    public ?string $mail_mailer;
    public ?string $mail_host;
    public ?int $mail_port;
    public ?string $mail_username;
    public ?string $mail_password;
    public ?string $mail_encryption;
    public ?string $mail_from_address;
    public ?string $mail_from_name;

    public ?string $whatsapp_api_url;
    public ?string $whatsapp_api_token;
    public ?string $whatsapp_phone_number_id;

    public static function group(): string
    {
        return 'communication';
    }
}
