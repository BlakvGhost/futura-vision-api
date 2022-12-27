<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['content', 'user_id'];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id');
    }

    public function viewers()
    {
        return $this->morphMany(Viewer::class, 'viewable');
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likable');
    }
}
