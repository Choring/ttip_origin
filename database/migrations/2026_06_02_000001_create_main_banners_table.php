<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('main_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');               // 배너 메인 문구
            $table->string('subtitle')->nullable(); // 장소/부제목
            $table->string('image_url');            // 배경 이미지 URL
            $table->string('link_url')->nullable(); // 클릭 시 이동 URL
            $table->boolean('is_active')->default(true);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('main_banners');
    }
};
