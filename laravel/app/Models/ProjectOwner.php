<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectOwner extends Model
{
    use HasFactory;

    public function user() {
        return $this->morphOne(User::class, 'typeable');
    }

    public function projects() {
        return $this->hasMany(Project::class)->where('country', $this->user->country);
    }
}
