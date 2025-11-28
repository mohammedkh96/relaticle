<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\EventResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Relaticle\SystemAdmin\Filament\Resources\EventResource;

final class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;
}
