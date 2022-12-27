<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'user_id', 'category_id', 'content', 'cover'];

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
