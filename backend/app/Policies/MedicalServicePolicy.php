<?php

namespace App\Policies;

use App\Models\MedicalService;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MedicalServicePolicy
{
    public function modify(User $user, MedicalService $medicalService): Response
    {
        return $user->id === $medicalService->medicalFacility->user_id
            ? Response::allow()
            : Response::deny('You do not own this medical service.');
    }
}
