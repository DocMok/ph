<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationToken extends Model
{
    use HasFactory;

    protected $guarded = [];
//    protected $appends = ['test'];

    public function getTestAttribute() {
        return 'test';
    }
}
