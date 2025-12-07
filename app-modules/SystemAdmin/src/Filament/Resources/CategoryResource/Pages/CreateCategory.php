<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\CategoryResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Relaticle\SystemAdmin\Filament\Resources\CategoryResource;

final class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}
