<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\VisitorResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Override;
use Relaticle\SystemAdmin\Filament\Resources\VisitorResource;

final class EditVisitor extends EditRecord
{
    protected static string $resource = VisitorResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
