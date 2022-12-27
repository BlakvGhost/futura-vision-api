<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'user_id'];

    public function user()
    {
        return $this->hasOne(User::class, 'id');
    }

    public function likable()
    {
        return $this->morphTo();
    }
}
