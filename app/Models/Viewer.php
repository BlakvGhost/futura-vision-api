<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Viewer extends Model
{
    use HasFactory;

    protected $fillable = ['ipaddress', 'user_agent'];

    public function viewable()
    {
        return $this->morphTo();
    }
}
