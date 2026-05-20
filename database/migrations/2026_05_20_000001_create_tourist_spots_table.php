<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tourist_spots', function (Blueprint $table) {
            $table->string('content_id')->primary();        // API 고유 ID
            $table->string('title');                        // 관광지 이름
            $table->string('addr1')->nullable();            // 주소
            $table->string('addr2')->nullable();            // 상세주소
            $table->string('image')->nullable();            // 대표 이미지
            $table->string('thumbnail')->nullable();        // 썸네일
            $table->string('map_x')->nullable();            // 경도
            $table->string('map_y')->nullable();            // 위도
            $table->string('tel')->nullable();              // 전화번호
            $table->text('overview')->nullable();           // 소개글
            $table->string('content_type_id')->default('12'); // 콘텐츠 유형
            $table->string('source')->default('api');       // 'api' or 'manual'
            $table->timestamp('fetched_at')->nullable();    // 마지막 API 수집 시각
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tourist_spots');
    }
};
