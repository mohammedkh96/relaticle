<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Policies;

use Relaticle\SystemAdmin\Models\SystemAdministrator;

final class ActivityPolicy
{
    /**
     * All admins can view activity logs
     */
    public function viewAny(): bool
    {
        return true;
    }

    public function view(): bool
    {
        return true;
    }

    public function create(): bool
    {
        return false; // Activity logs are created automatically
    }

    public function update(): bool
    {
        return false; // Activity logs should not be edited
    }

    public function delete(): bool
    {
        return false; // Activity logs should not be deleted
    }

    public function deleteAny(): bool
    {
        return false;
    }

    public function restore(): bool
    {
        return false;
    }

    public function forceDelete(): bool
    {
        return false;
    }
}
