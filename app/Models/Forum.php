<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'category_id', 'content'];

    public function category()
    {
        return $this->hasOne(Category::class, 'id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
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
