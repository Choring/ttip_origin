<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title', 200);
            $table->longText('content');
            $table->json('summary'); // 3줄 요약 배열 저장 (['요약1', '요약2', '요약3'])
            $table->string('card_image_path')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->enum('type', ['general', 'ad', 'notice'])->default('general');
            $table->timestamps();
            
            $table->index(['type', 'created_at']); // 검색 및 필터링 최적화
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
