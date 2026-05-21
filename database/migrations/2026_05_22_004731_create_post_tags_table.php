<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->string('tag', 30);

            $table->index('tag');
            $table->index('post_id');
            $table->unique(['post_id', 'tag']);  // 동일 게시글에 동일 태그 중복 방지

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });

        // 기존 게시글의 tags JSON 컬럼에서 post_tags 테이블로 데이터 이전
        $posts = DB::table('posts')
            ->whereNotNull('tags')
            ->where('tags', '!=', '[]')
            ->where('tags', '!=', 'null')
            ->select('id', 'tags')
            ->get();

        $rows = [];
        foreach ($posts as $post) {
            $tags = json_decode($post->tags, true);
            if (!is_array($tags)) continue;

            foreach ($tags as $tag) {
                $tag = trim((string) $tag);
                if ($tag === '') continue;

                $rows[] = [
                    'post_id' => $post->id,
                    'tag'     => $tag,
                ];
            }
        }

        if (!empty($rows)) {
            // unique 제약 충돌 방지를 위해 중복 제거 후 insert
            $unique = collect($rows)->unique(fn($r) => $r['post_id'] . '_' . $r['tag'])->values()->toArray();
            foreach (array_chunk($unique, 500) as $chunk) {
                DB::table('post_tags')->insert($chunk);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('post_tags');
    }
};
