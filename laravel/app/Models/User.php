<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const PROJECT_OWNER = 'ProjectOwner';
    const INVESTOR = 'Investor';
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'typeable_type',
        'typeable_id',
        'email_verified_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['user_type'];

    public function typeable()
    {
        return $this->morphTo();
    }

    public function getUserTypeAttribute()
    {
        return class_basename($this->typeable);
    }

    public function likedProjects()
    {
        return $this->belongsToMany(Project::class, 'project_user_likes');
    }

    public function likedInvestors()
    {
        return $this->belongsToMany(Investor::class, 'investor_user_likes');
    }

    public function notificationTokens()
    {
        return $this->hasMany(NotificationToken::class);
    }

    public function notices()
    {
        return $this->hasMany(Notice::class, 'to_user_id', 'id');
    }
}
