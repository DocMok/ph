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

    public function scopeFilter($query, $request, User $user)
    {
        return $query->when($request->category_ids, function ($query) use ($request) {
            $query->whereHas('categories', function ($query) use ($request) {
                $query->whereIn('category_id', json_decode($request->category_ids));
            });
        })
            ->when(!$request->category_ids && $user->user_type == User::PROJECT_OWNER, function ($query) use ($user) {
                $lastProject = $user->typeable->projects()->orderBy('created_at', 'desc')->first();
                $lastProject ? $query->whereHas('categories', function ($query) use ($lastProject) {
                    $query->where('category_id', $lastProject->category_id);
                }) : $query;
            })
            ->when($request->currency, function ($query) use ($request) {
                $query->where('currency', $request->currency);
            })
            ->when($request->min, function ($query) use ($request) {
                $query->where('amount', '>=', $request->min);
            })
            ->when($request->max, function ($query) use ($request) {
                $query->where('amount', '<=', $request->max);
            })->orderBy('created_at', 'desc');
    }
}
