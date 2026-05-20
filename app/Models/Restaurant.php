<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $primaryKey = 'content_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'content_id',
        'title',
        'category',
        'address',
        'image',
        'extra_images',
        'homepage',
        'tel',
        'map_x',
        'map_y',
    ];

    protected $casts = [
        'extra_images' => 'array',
    ];
}
