<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['sup_title', 'sub_title', 'content', 'cover', 'user_id'];

    public function user()
    {
        return $this->hasOne(User::class, 'id');
    }
    
}
