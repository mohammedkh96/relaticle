<?php

namespace App\Policies;

use App\Models\Payment;
use Relaticle\SystemAdmin\Models\SystemAdministrator;

class PaymentPolicy
{
    public function viewAny(SystemAdministrator $user): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('view_payments');
    }

    public function view(SystemAdministrator $user, Payment $payment): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('view_payments');
    }

    public function create(SystemAdministrator $user): bool
    {
        return $user->role->canCreate();
    }

    public function update(SystemAdministrator $user, Payment $payment): bool
    {
        return $user->role->canEdit();
    }

    public function delete(SystemAdministrator $user, Payment $payment): bool
    {
        return $user->role->canDelete();
    }

    public function deleteAny(SystemAdministrator $user): bool
    {
        return $user->role->canDelete();
    }

    public function restore(SystemAdministrator $user, Payment $payment): bool
    {
        return $user->role->canDelete();
    }

    public function forceDelete(SystemAdministrator $user, Payment $payment): bool
    {
        return $user->role->isSuperAdmin();
    }
}
