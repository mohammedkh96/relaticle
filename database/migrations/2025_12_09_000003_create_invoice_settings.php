<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('invoice.company_name', config('app.name'));
        $this->migrator->add('invoice.company_address', '');
        $this->migrator->add('invoice.company_phone', '');
        $this->migrator->add('invoice.company_logo', null);
        $this->migrator->add('invoice.invoice_note', '');
    }

    public function down(): void
    {
        $this->migrator->delete('invoice.company_name');
        $this->migrator->delete('invoice.company_address');
        $this->migrator->delete('invoice.company_phone');
        $this->migrator->delete('invoice.company_logo');
        $this->migrator->delete('invoice.invoice_note');
    }
};
