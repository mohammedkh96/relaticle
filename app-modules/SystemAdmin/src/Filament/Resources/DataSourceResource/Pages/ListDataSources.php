<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\DataSourceResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Relaticle\SystemAdmin\Filament\Resources\DataSourceResource;

final class ListDataSources extends ListRecords
{
    protected static string $resource = DataSourceResource::class;
}
