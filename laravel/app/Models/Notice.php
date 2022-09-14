<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = ['notice_type'];

    const PROJECT = 'Project';
    const INVESTOR = 'Investor';

    public function notificateable()
    {
        return $this->morphTo();
    }

    public function getNoticeTypeAttribute()
    {
        return class_basename($this->notificateable);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }
}
