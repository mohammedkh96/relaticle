<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\DataSourceResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Relaticle\SystemAdmin\Filament\Resources\DataSourceResource;

final class CreateDataSource extends CreateRecord
{
    protected static string $resource = DataSourceResource::class;
}
