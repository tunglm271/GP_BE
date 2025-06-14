<?php

namespace App\Policies;

use App\Models\Role\Patient;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Models\Sale;
class SalePolicy
{
    public function modify(User $user, Sale $sale): Response
    {
        return $user->medicalFacility->id === $sale->medicalFacility->id
            ? Response::allow()
            : Response::deny('Method not allowed.');
    }
}
