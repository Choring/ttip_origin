<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainBanner extends Model
{
    protected $fillable = [
        'title', 'subtitle',
        'image_url', 'link_url',
        'is_active', 'sort_order',
        'started_at', 'ended_at',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
    ];

    // 현재 활성화된 배너만 (날짜 범위 포함)
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('started_at')->orWhere('started_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ended_at')->orWhere('ended_at', '>=', now());
            })
            ->orderBy('sort_order');
    }
}
