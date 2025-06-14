<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Notification extends Model
{
    use HasFactory;

    // 👇 Thêm dòng này để Laravel không hiểu lầm là auto-increment
    public $incrementing = false;

    // 👇 Thêm dòng này để id là string (UUID)
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected $fillable = [
        'type',
        'title',
        'message',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function userNotifications()
    {
        return $this->hasMany(UserNotification::class);
    }
}