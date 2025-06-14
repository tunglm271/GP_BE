<?php

namespace App\Policies;

use App\Models\Role\Doctor;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DoctorPolicy
{
    public function modify(User $user, Doctor $doctor): Response
    {
        return $user->id === $doctor->user_id
            ? Response::allow()
            : Response::deny('You are not this doctor.');
    }
}
