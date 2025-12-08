<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OpportunityStatus: string implements HasLabel, HasColor, HasIcon
{
    case New = 'new';
    case Contacted = 'contacted';
    case Interested = 'interested';
    case Negotiation = 'negotiation';
    case Won = 'won';
    case Lost = 'lost';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::New => 'New',
            self::Contacted => 'Contacted',
            self::Interested => 'Interested',
            self::Negotiation => 'Negotiation',
            self::Won => 'Won',
            self::Lost => 'Lost',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::New => 'gray',
            self::Contacted => 'info',
            self::Interested => 'warning',
            self::Negotiation => 'primary',
            self::Won => 'success',
            self::Lost => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::New => 'heroicon-m-sparkles',
            self::Contacted => 'heroicon-m-chat-bubble-left-ellipsis',
            self::Interested => 'heroicon-m-hand-thumb-up',
            self::Negotiation => 'heroicon-m-currency-dollar',
            self::Won => 'heroicon-m-check-circle',
            self::Lost => 'heroicon-m-x-circle',
        };
    }
}
