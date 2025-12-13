<?php

namespace App\Policies;

use App\Models\Invoice;
use Relaticle\SystemAdmin\Models\SystemAdministrator;

class InvoicePolicy
{
    public function viewAny(SystemAdministrator $user): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('view_invoices');
    }

    public function view(SystemAdministrator $user, Invoice $invoice): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('view_invoices');
    }

    public function create(SystemAdministrator $user): bool
    {
        return $user->role->canCreate();
    }

    public function update(SystemAdministrator $user, Invoice $invoice): bool
    {
        return $user->role->canEdit();
    }

    public function delete(SystemAdministrator $user, Invoice $invoice): bool
    {
        return $user->role->canDelete();
    }

    public function deleteAny(SystemAdministrator $user): bool
    {
        return $user->role->canDelete();
    }

    public function restore(SystemAdministrator $user, Invoice $invoice): bool
    {
        return $user->role->canDelete();
    }

    public function forceDelete(SystemAdministrator $user, Invoice $invoice): bool
    {
        return $user->role->isSuperAdmin();
    }
}
