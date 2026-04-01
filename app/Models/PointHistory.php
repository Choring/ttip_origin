<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'reference_table',
        'reference_id',
    ];

    /**
     * Get the user that owns the point history log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
