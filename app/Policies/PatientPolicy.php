<?php

namespace App\Policies;

use App\Models\Role\Patient;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PatientPolicy
{
    public function modify(User $user, Patient $patient): Response
    {
        return $user->id === $patient->user_id
            ? Response::allow()
            : Response::deny('You are not this patient.');
    }
}
