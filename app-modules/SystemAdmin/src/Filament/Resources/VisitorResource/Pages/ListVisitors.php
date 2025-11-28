<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\VisitorResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;
use Relaticle\SystemAdmin\Filament\Resources\VisitorResource;

final class ListVisitors extends ListRecords
{
    protected static string $resource = VisitorResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
