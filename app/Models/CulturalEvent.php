<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CulturalEvent extends Model
{
    protected $primaryKey = 'event_seq';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'event_seq',
        'subject',
        'event_gubun',
        'start_date',
        'end_date',
        'place',
        'pay',
        'content',
        'homepage',
    ];
}
