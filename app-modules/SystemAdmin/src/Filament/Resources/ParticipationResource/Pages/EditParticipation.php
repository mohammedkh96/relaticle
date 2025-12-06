<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\ParticipationResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Override;
use Relaticle\SystemAdmin\Filament\Resources\ParticipationResource;

final class EditParticipation extends EditRecord
{
    protected static string $resource = ParticipationResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
