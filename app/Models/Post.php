<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use HasFactory;

    protected $appends = ['card_image_url'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'content',
        'summary',
        'extra_info',
        'card_image_path',
        'view_count',
        'type',
        'tags',
        'is_pinned',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'summary' => 'array',
            'extra_info' => 'array',
            'tags' => 'array',
            'is_pinned' => 'boolean',
        ];
    }

    /**
     * Scope a query to only include pinned posts.
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Scope a query to only include notices.
     */
    public function scopeNotice($query)
    {
        return $query->where('type', 'notice');
    }

    /**
     * Get the user that owns the post.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that owns the post.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(PostBookmark::class);
    }


    /**
     * 전체 이미지 URL 반환 (NCP 또는 Local 환경에 따라 자동 대응)
     */
    protected function cardImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => !empty($this->card_image_path)
                ? Storage::disk(config('filesystems.default'))->url($this->card_image_path) 
                : null,
        );
    }
}
