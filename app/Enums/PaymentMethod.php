<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel
{
    case BANK_TRANSFER = 'bank_transfer';
    case CASH = 'cash';
    case CHECK = 'check';
    case STRIPE = 'stripe';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::BANK_TRANSFER => 'Bank Transfer',
            self::CASH => 'Cash',
            self::CHECK => 'Check',
            self::STRIPE => 'Stripe',
        };
    }
}
