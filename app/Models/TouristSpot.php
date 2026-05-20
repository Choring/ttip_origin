<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristSpot extends Model
{
    protected $primaryKey = 'content_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'content_id',
        'title',
        'addr1',
        'addr2',
        'image',
        'thumbnail',
        'map_x',
        'map_y',
        'tel',
        'overview',
        'usetime',
        'restdate',
        'usefee',
        'parking',
        'chkpet',
        'chkbabycarriage',
        'extra_images',
        'content_type_id',
        'source',
        'fetched_at',
    ];

    protected $casts = [
        'fetched_at'   => 'datetime',
        'extra_images' => 'array',
    ];

    // API 데이터만
    public function scopeFromApi($query)
    {
        return $query->where('source', 'api');
    }

    // 관리자가 직접 추가한 데이터만
    public function scopeManual($query)
    {
        return $query->where('source', 'manual');
    }
}
