<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Policies;

use App\Models\Participation;
use Relaticle\SystemAdmin\Models\SystemAdministrator;

final class ParticipationPolicy
{
    public function viewAny(SystemAdministrator $admin): bool
    {
        return $admin->role->isSuperAdmin() || $admin->hasPermission('view_participations');
    }

    public function view(SystemAdministrator $admin, Participation $model): bool
    {
        return $admin->role->isSuperAdmin() || $admin->hasPermission('view_participations');
    }

    public function create(SystemAdministrator $admin): bool
    {
        return $admin->role->isSuperAdmin() || $admin->hasPermission('create_participations');
    }

    public function update(SystemAdministrator $admin, Participation $model): bool
    {
        return $admin->role->isSuperAdmin() || $admin->hasPermission('edit_participations');
    }

    public function delete(SystemAdministrator $admin, Participation $model): bool
    {
        return $admin->role->isSuperAdmin() || $admin->hasPermission('delete_participations');
    }

    public function deleteAny(SystemAdministrator $admin): bool
    {
        return $admin->role->isSuperAdmin() || $admin->hasPermission('delete_participations');
    }

    public function restore(SystemAdministrator $admin, Participation $model): bool
    {
        return $admin->role->isSuperAdmin() || $admin->hasPermission('delete_participations');
    }

    public function forceDelete(SystemAdministrator $admin, Participation $model): bool
    {
        return $admin->role->isSuperAdmin();
    }

    public function restoreAny(SystemAdministrator $admin): bool
    {
        return $admin->role->canDelete();
    }

    public function forceDeleteAny(SystemAdministrator $admin): bool
    {
        return $admin->role->isSuperAdmin();
    }
}
