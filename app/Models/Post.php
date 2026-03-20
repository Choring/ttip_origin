<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'summary',
        'card_image_path',
        'view_count',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'summary' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
