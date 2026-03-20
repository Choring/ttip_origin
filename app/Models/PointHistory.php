<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointHistory extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'reference_table',
        'reference_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
