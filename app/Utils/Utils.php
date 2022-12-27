<?php

namespace App\Utils;

use Illuminate\Support\Str;

class Utils
{
    static function store_image($request, $title, $to, $file)
    {
        $imageName = preg_replace('/[^A-Za-z0-9\-]/', '-', Str::lower($title))
              . '-' . date('Y-m-d-H-s-i') . '.' . $request->file($file)->extension();
        return $request->file($file)->storeAs($to, $imageName, 'public');
    }
}
