<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\EventResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Override;
use Relaticle\SystemAdmin\Filament\Resources\EventResource;

final class ViewEvent extends ViewRecord
{
    protected static string $resource = EventResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
