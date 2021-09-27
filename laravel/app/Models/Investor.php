<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investor extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->morphOne(User::class, 'typeable');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'investor_user_likes');
    }

    public function notices()
    {
        return $this->morphMany(Notice::class, 'notificateable');
    }
}
