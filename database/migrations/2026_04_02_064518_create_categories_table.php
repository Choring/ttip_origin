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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('메뉴 표시 이름');
            $table->string('slug')->unique()->comment('URL 식별자');
            $table->text('description')->nullable()->comment('부연 설명');
            $table->integer('sort_order')->default(0)->comment('정렬 번호');
            $table->boolean('is_active')->default(true)->comment('활성화 유무');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
