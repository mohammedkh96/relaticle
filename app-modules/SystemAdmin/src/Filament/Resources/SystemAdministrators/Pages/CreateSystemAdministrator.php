<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\SystemAdministrators\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Relaticle\SystemAdmin\Filament\Resources\SystemAdministrators\SystemAdministratorResource;

final class CreateSystemAdministrator extends CreateRecord
{
    protected static string $resource = SystemAdministratorResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCancelAction(),
        ];
    }

    private function getCancelAction(): Action
    {
        return Action::make('cancel')
            ->label('Cancel')
            ->url($this->getResource()::getUrl('index'))
            ->color('gray');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $permissions = [];
        $resources = \Relaticle\SystemAdmin\Models\SystemAdministrator::getManageableResources();

        foreach ($resources as $resourceKey => $label) {
            $field = "permissions_{$resourceKey}";
            if (isset($data[$field])) {
                $permissions = array_merge($permissions, $data[$field]);
                unset($data[$field]);
            }
        }

        if ($data['role'] !== \Relaticle\SystemAdmin\Enums\SystemAdministratorRole::SuperAdministrator->value) {
            $data['permissions'] = $permissions;
        } else {
            $data['permissions'] = null;
        }

        return $data;
    }
}
