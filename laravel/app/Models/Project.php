<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

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
        return $this->belongsToMany(User::class, 'project_user_likes')
            ->withTimestamps()->orderByPivot('created_at', 'desc');
    }

    public function notices()
    {
        return $this->morphMany(Notice::class, 'notificateable');
    }

    public function scopeSuggestions($query, User $user)
    {
        return $query->where('country', $user->country)
            ->when($user->user_type == User::INVESTOR, function ($query) use ($user) {
            $query->whereIn('category_id', $user->typeable->categories->keyBy('id')->keys());
        })
            ->when($user->user_type == User::PROJECT_OWNER, function ($query) use ($user) {
                $projectCategories = $user->typeable->projects->keyBy('category_id')->keys();
                $projectCategories ? $query->whereIn('category_id', $projectCategories) : $query;
            })
            ->orderBy('id', 'desc');
    }

    public function scopeFilter($query, $request, User $user)
    {
        return $query->where('country', $user->country)
            ->when($request->category_ids, function ($query) use ($request) {
                $query->whereIn('category_id', json_decode($request->category_ids));
            })
            ->when(!$request->category_ids && $user->user_type == User::INVESTOR, function ($query) use ($user) {
                $query->whereIn('category_id', $user->typeable->categories->keyBy('id')->keys());
            })
            ->when($request->currency, function ($query) use ($request) {
                $query->where('currency', $request->currency);
            })
            ->when($request->min, function ($query) use ($request) {
                $query->where('amount_remaining', '>=', $request->min);
            })
            ->when($request->max, function ($query) use ($request) {
                $query->where('amount_remaining', '<=', $request->max);
            })->orderBy('created_at', 'desc');
    }
}
