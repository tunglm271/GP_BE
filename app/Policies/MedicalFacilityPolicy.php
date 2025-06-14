<?php

namespace App\Policies;

use App\Models\Role\MedicalFacility;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MedicalFacilityPolicy
{
    public function modify(User $user, MedicalFacility $medicalFacility): Response
    {
        return $user->id === $medicalFacility->user_id
            ? Response::allow()
            : Response::deny('You are not this medical facility.');
    }
}
