<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\VisitorResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Override;
use Relaticle\SystemAdmin\Filament\Resources\VisitorResource;

final class ViewVisitor extends ViewRecord
{
    protected static string $resource = VisitorResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
