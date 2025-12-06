<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\ParticipationResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;
use Relaticle\SystemAdmin\Filament\Resources\ParticipationResource;

final class ListParticipations extends ListRecords
{
    protected static string $resource = ParticipationResource::class;

    protected function modifyQueryUsing($query)
    {
        return $query->with(['event', 'visitor', 'company']);
    }

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
