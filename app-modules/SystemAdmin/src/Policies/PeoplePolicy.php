<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Policies;

use Relaticle\SystemAdmin\Models\SystemAdministrator;

final class PeoplePolicy
{
    public function viewAny(SystemAdministrator $admin): bool
    {
        return $admin->role->isSuperAdmin() || $admin->hasPermission('view_people');
    }

    public function view(): bool
    {
        return true;
    }

    public function create(SystemAdministrator $admin): bool
    {
        return $admin->role->canCreate();
    }

    public function update(SystemAdministrator $admin): bool
    {
        return $admin->role->canEdit();
    }

    public function delete(SystemAdministrator $admin): bool
    {
        return $admin->role->canDelete();
    }

    public function deleteAny(SystemAdministrator $admin): bool
    {
        return $admin->role->canDelete();
    }

    public function restore(SystemAdministrator $admin): bool
    {
        return $admin->role->canDelete();
    }

    public function forceDelete(SystemAdministrator $admin): bool
    {
        return $admin->role->isSuperAdmin();
    }

    public function forceDeleteAny(SystemAdministrator $admin): bool
    {
        return $admin->role->isSuperAdmin();
    }

    public function restoreAny(SystemAdministrator $admin): bool
    {
        return $admin->role->canDelete();
    }
}
