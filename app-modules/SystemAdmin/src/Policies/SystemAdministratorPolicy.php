<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Policies;

use Illuminate\Auth\Access\Response;
use Relaticle\SystemAdmin\Models\SystemAdministrator;

final class SystemAdministratorPolicy
{
    public function viewAny(SystemAdministrator $admin): bool
    {
        return $admin->role->canManageAdmins();
    }

    public function view(SystemAdministrator $admin, SystemAdministrator $systemAdmin): bool
    {
        return $admin->role->canManageAdmins() || $admin->id === $systemAdmin->id;
    }

    public function create(SystemAdministrator $admin): Response
    {
        return $admin->role->canManageAdmins()
            ? Response::allow()
            : Response::deny('Only Super Administrators can create new system administrators.');
    }

    public function update(SystemAdministrator $admin, SystemAdministrator $systemAdmin): Response
    {
        // Super admins can edit anyone
        if ($admin->role->canManageAdmins()) {
            return Response::allow();
        }

        // Admins can only edit their own account
        if ($admin->id === $systemAdmin->id && $admin->role->canEdit()) {
            return Response::allow();
        }

        return Response::deny('You can only edit your own account.');
    }

    public function delete(SystemAdministrator $admin, SystemAdministrator $systemAdmin): Response
    {
        if ($admin->id === $systemAdmin->id) {
            return Response::deny('You cannot delete your own account.');
        }

        if ($admin->role->canManageAdmins()) {
            return Response::allow();
        }

        return Response::deny('Only Super Administrators can delete system administrators.');
    }

    public function deleteAny(SystemAdministrator $admin): bool
    {
        return $admin->role->canManageAdmins();
    }

    public function restore(SystemAdministrator $admin): bool
    {
        return $admin->role->canManageAdmins();
    }

    public function forceDelete(SystemAdministrator $admin, SystemAdministrator $systemAdmin): Response
    {
        if ($admin->id === $systemAdmin->id) {
            return Response::deny('You cannot permanently delete your own account.');
        }

        return $admin->role->canManageAdmins()
            ? Response::allow()
            : Response::deny('Only Super Administrators can permanently delete system administrators.');
    }

    public function forceDeleteAny(SystemAdministrator $admin): bool
    {
        return $admin->role->canManageAdmins();
    }

    public function restoreAny(SystemAdministrator $admin): bool
    {
        return $admin->role->canManageAdmins();
    }
}
