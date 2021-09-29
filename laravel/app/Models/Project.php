<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function projectOwner()
    {
        return $this->belongsTo(ProjectOwner::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'project_user_likes');
    }

    public function notices()
    {
        return $this->morphMany(Notice::class, 'notificateable');
    }

    public function scopeSuggestions($query, User $user)
    {
        return $query->when($user->user_type == User::INVESTOR, function ($query) use ($user) {
            $query->whereIn('category_id', $user->typeable->categories->keyBy('id')->keys())
            ->where('amount_remaining', '<=', $user->typeable->amount)
            ->where('currency', $user->typeable->currency);
        })
            ->when($user->user_type == User::PROJECT_OWNER, function ($query) use ($user) {
                $lastProject = $user->typeable->projects()->orderBy('created_at', 'desc')->first();
                $lastProject ? $query->where('category_id', $lastProject->category_id) : $query;
            });
    }
}
