<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\VisitorResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Relaticle\SystemAdmin\Filament\Resources\VisitorResource;

final class CreateVisitor extends CreateRecord
{
    protected static string $resource = VisitorResource::class;
}
