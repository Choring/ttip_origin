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
        Schema::create('cultural_events', function (Blueprint $table) {
            $table->string('event_seq')->primary(); // 고유 식별자 (API 제공)
            $table->string('subject'); // 행사 제목
            $table->string('event_gubun')->nullable(); // 분류 (공연, 전시 등)
            $table->date('start_date'); // 시작일
            $table->date('end_date'); // 종료일
            $table->string('place')->nullable(); // 장소
            $table->string('pay')->nullable(); // 요금 정보 (유료, 무료 등)
            $table->text('content')->nullable(); // 설명
            $table->string('homepage')->nullable(); // 홈페이지 링크
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cultural_events');
    }
};
