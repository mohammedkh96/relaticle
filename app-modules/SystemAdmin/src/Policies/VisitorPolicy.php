<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Policies;

use App\Models\Visitor;
use Relaticle\SystemAdmin\Models\SystemAdministrator;

final class VisitorPolicy
{
    public function viewAny(SystemAdministrator $admin): bool
    {
        return true;
    }

    public function view(SystemAdministrator $admin, Visitor $model): bool
    {
        return true;
    }

    public function create(SystemAdministrator $admin): bool
    {
        return true;
    }

    public function update(SystemAdministrator $admin, Visitor $model): bool
    {
        return true;
    }

    public function delete(SystemAdministrator $admin, Visitor $model): bool
    {
        return true;
    }

    public function deleteAny(SystemAdministrator $admin): bool
    {
        return true;
    }

    public function restore(SystemAdministrator $admin, Visitor $model): bool
    {
        return true;
    }

    public function forceDelete(SystemAdministrator $admin, Visitor $model): bool
    {
        return $admin->role->isSuperAdmin();
    }
}
