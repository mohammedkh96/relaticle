<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\SystemAdministrators\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Relaticle\SystemAdmin\Filament\Resources\SystemAdministrators\SystemAdministratorResource;

final class EditSystemAdministrator extends EditRecord
{
    protected static string $resource = SystemAdministratorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $permissions = [];
        $resources = \Relaticle\SystemAdmin\Models\SystemAdministrator::getManageableResources();

        foreach ($resources as $resourceKey => $label) {
            $field = "permissions_{$resourceKey}";
            if (isset($data[$field])) {
                $permissions = array_merge($permissions, $data[$field]);
                unset($data[$field]);
            }
            // Also ensure we clean up the key if it was set but empty?
            // isset checks if key exists.
        }

        if ($data['role'] !== \Relaticle\SystemAdmin\Enums\SystemAdministratorRole::SuperAdministrator->value) {
            $data['permissions'] = $permissions;
        } else {
            $data['permissions'] = null; // or empty array
        }

        return $data;
    }
}
