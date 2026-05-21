<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $fillable = [
        'name', 'email', 'subject', 'content',
        'status', 'answer', 'answered_at', 'answered_by',
    ];

    protected $casts = [
        'answered_at' => 'datetime',
    ];

    public function answeredBy()
    {
        return $this->belongsTo(User::class, 'answered_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
