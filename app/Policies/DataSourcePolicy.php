<?php

namespace App\Policies;

use App\Models\DataSource;
use Illuminate\Foundation\Auth\User;

class DataSourcePolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, DataSource $dataSource): bool
    {
        return true;
    }

    public function create(?User $user): bool
    {
        return true;
    }

    public function update(?User $user, DataSource $dataSource): bool
    {
        return true;
    }

    public function delete(?User $user, DataSource $dataSource): bool
    {
        return true;
    }

    public function deleteAny(?User $user): bool
    {
        return true;
    }

    public function restore(?User $user, DataSource $dataSource): bool
    {
        return true;
    }

    public function restoreAny(?User $user): bool
    {
        return true;
    }

    public function forceDelete(?User $user, DataSource $dataSource): bool
    {
        return true;
    }

    public function forceDeleteAny(?User $user): bool
    {
        return true;
    }

    public function reorder(?User $user): bool
    {
        return true;
    }
}
