<?php

namespace App\Policies;

use App\Models\HealthProfile;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class HealthProfilePolicy
{
    public function modify(User $user, HealthProfile $healthProfile)
    {
        return $user->id === $healthProfile->patient()->user_id
            ? Response::allow()
            : Response::deny('You do not own this health profile.');
    }
}
