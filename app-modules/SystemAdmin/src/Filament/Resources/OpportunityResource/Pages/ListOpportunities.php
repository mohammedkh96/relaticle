<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\OpportunityResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;
use Relaticle\SystemAdmin\Filament\Resources\OpportunityResource;

final class ListOpportunities extends ListRecords
{
    protected static string $resource = OpportunityResource::class;

    protected function modifyQueryUsing($query)
    {
        return $query->with(['company']);
    }

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
