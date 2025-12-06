<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\EventResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;
use Relaticle\SystemAdmin\Filament\Resources\EventResource;

final class ListEvents extends ListRecords
{
    protected static string $resource = EventResource::class;

    protected function modifyQueryUsing($query)
    {
        return $query->with(['participations']);
    }

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
