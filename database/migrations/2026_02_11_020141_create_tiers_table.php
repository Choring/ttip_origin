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
        Schema::create('tiers', function (Blueprint $table) {
            $table->comment('사용자의 등급(티어) 정보를 저장하는 테이블');
            $table->id();
            $table->string('name', 20);      // 씨앗, 고수 등
            $table->integer('min_points')->default(0)->index(); // 등급 달성 최소 포인트
            $table->string('icon_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiers');
    }
};
