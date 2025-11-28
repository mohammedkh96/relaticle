<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\ParticipationResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Override;
use Relaticle\SystemAdmin\Filament\Resources\ParticipationResource;

final class ViewParticipation extends ViewRecord
{
    protected static string $resource = ParticipationResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
