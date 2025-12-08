<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EventStatus: string implements HasLabel, HasColor
{
    case UPCOMING = 'upcoming';
    case RUNNING = 'running';
    case FINISHED = 'finished';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::UPCOMING => 'Upcoming',
            self::RUNNING => 'Running',
            self::FINISHED => 'Finished',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::UPCOMING => 'info',
            self::RUNNING => 'success',
            self::FINISHED => 'gray',
        };
    }
}
