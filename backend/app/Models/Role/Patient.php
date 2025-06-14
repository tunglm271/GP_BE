<?php

namespace App\Models\Role;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\HealthProfile;
class Patient extends Model
{
    /** @use HasFactory<\Database\Factories\Role\PatientFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'address',
    ];

    protected $appends = [
        'email',
        'number_of_health_profiles',
    ];

    public function user()
    {  
        return $this->belongsTo(User::class);
    }

    public function healthProfiles()
    {
        return $this->hasMany(HealthProfile::class);
    }

    public function getEmailAttribute()
    {
        return $this->user->email;
    }

    public function getNumberOfHealthProfilesAttribute()
    {
        return $this->healthProfiles()->count();
    }
}
