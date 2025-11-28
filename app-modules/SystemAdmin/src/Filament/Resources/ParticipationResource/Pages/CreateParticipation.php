<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\ParticipationResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Relaticle\SystemAdmin\Filament\Resources\ParticipationResource;

final class CreateParticipation extends CreateRecord
{
    protected static string $resource = ParticipationResource::class;
}
