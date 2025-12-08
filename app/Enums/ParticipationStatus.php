<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ParticipationStatus: string implements HasLabel, HasColor
{
    case RESERVED = 'reserved';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::RESERVED => 'Reserved',
            self::CONFIRMED => 'Confirmed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::RESERVED => 'warning',
            self::CONFIRMED => 'success',
            self::CANCELLED => 'danger',
        };
    }
}
