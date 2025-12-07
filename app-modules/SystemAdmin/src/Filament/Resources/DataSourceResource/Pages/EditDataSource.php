<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\DataSourceResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Relaticle\SystemAdmin\Filament\Resources\DataSourceResource;

final class EditDataSource extends EditRecord
{
    protected static string $resource = DataSourceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
