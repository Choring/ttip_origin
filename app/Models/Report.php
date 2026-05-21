<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'reportable_type',
        'reportable_id',
        'reason',
        'description',
        'status',
        'admin_note',
    ];

    // 신고한 유저
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 신고 대상 (Post 또는 Comment)
    public function reportable()
    {
        return $this->morphTo();
    }

    // 사유 레이블
    public static function reasonLabel(string $reason): string
    {
        return match($reason) {
            'spam'    => '스팸/광고',
            'abuse'   => '욕설/비방',
            'obscene' => '음란물',
            'illegal' => '불법 정보',
            'other'   => '기타',
            default   => $reason,
        };
    }

    // 상태 레이블
    public static function statusLabel(string $status): string
    {
        return match($status) {
            'pending'   => '처리 대기',
            'resolved'  => '처리 완료',
            'dismissed' => '기각',
            default     => $status,
        };
    }
}
