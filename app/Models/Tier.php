<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tier extends Model
{
    protected $fillable = [
        'name',
        'min_points',
        'icon_url',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
