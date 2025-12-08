<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class InvoiceSettings extends Settings
{
    public ?string $company_name;
    public ?string $company_address;
    public ?string $company_phone;
    public ?string $company_logo;
    public ?string $invoice_note;

    public static function group(): string
    {
        return 'invoice';
    }
}
