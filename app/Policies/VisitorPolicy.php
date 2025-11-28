<?php

namespace App\Policies;

use App\Models\Visitor;
use Illuminate\Auth\Access\HandlesAuthorization;

class VisitorPolicy
{
    use HandlesAuthorization;

    public function viewAny($user): bool
    {
        return true;
    }

    public function view($user, Visitor $visitor): bool
    {
        return true;
    }

    public function create($user): bool
    {
        return true;
    }

    public function update($user, Visitor $visitor): bool
    {
        return true;
    }

    public function delete($user, Visitor $visitor): bool
    {
        return true;
    }

    public function deleteAny($user): bool
    {
        return true;
    }

    public function restore($user, Visitor $visitor): bool
    {
        return true;
    }

    public function forceDelete($user, Visitor $visitor): bool
    {
        return true;
    }
}
