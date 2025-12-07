<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\CategoryResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Relaticle\SystemAdmin\Filament\Resources\CategoryResource;

final class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;
}
