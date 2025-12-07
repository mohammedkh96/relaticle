<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\CategoryResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Relaticle\SystemAdmin\Filament\Resources\CategoryResource;

final class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
