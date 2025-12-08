<?php

namespace App\Policies;

use App\Models\Payment;
use Illuminate\Foundation\Auth\User;

class PaymentPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Payment $payment): bool
    {
        return true;
    }

    public function create(?User $user): bool
    {
        return true;
    }

    public function update(?User $user, Payment $payment): bool
    {
        return true;
    }

    public function delete(?User $user, Payment $payment): bool
    {
        return true;
    }

    public function deleteAny(?User $user): bool
    {
        return true;
    }

    public function restore(?User $user, Payment $payment): bool
    {
        return true;
    }

    public function forceDelete(?User $user, Payment $payment): bool
    {
        return true;
    }
}
