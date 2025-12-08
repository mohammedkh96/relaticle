<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentType: string implements HasLabel
{
    case DEPOSIT = 'deposit';
    case FINAL = 'final';
    case ADDITIONAL = 'additional';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DEPOSIT => 'Deposit (50%)',
            self::FINAL => 'Final Payment',
            self::ADDITIONAL => 'Additional',
        };
    }
}
