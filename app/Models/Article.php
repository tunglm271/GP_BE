<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'cover_image',
        'status',
        'views',
    ];

    public function scopeTopFavorite($query, $limit = 5)
    {
        return $query->orderBy('views', 'desc')->where('status','published')->take($limit);
    }

    public function scopeRecent($query, $limit = 5)
    {
        return $query->orderBy('created_at', 'desc')->where('status','published')->take($limit);
    }
}
