<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Policies;

use Relaticle\SystemAdmin\Models\SystemAdministrator;

final class NotePolicy
{
    public function viewAny(SystemAdministrator $admin): bool
    {
        return $admin->role->isSuperAdmin() || $admin->hasPermission('view_notes');
    }

    public function view(SystemAdministrator $admin, SystemAdministrator $model): bool
    {
        return $admin->role->isSuperAdmin() || $admin->hasPermission('view_notes');
    }

    public function create(SystemAdministrator $admin): bool
    {
        return $admin->role->isSuperAdmin() || $admin->hasPermission('create_notes');
    }

    public function update(SystemAdministrator $admin): bool
    {
        return $admin->role->isSuperAdmin() || $admin->hasPermission('edit_notes');
    }

    public function delete(SystemAdministrator $admin): bool
    {
        return $admin->role->isSuperAdmin() || $admin->hasPermission('delete_notes');
    }

    public function deleteAny(SystemAdministrator $admin): bool
    {
        return $admin->role->isSuperAdmin() || $admin->hasPermission('delete_notes');
    }

    public function restore(SystemAdministrator $admin): bool
    {
        return $admin->role->isSuperAdmin() || $admin->hasPermission('delete_notes');
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
