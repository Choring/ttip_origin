<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->string('dialect');           // 사투리 문장/단어
            $table->string('answer');            // 정답 (표준어)
            $table->string('wrong1');            // 오답 1
            $table->string('wrong2');            // 오답 2
            $table->text('explanation')->nullable(); // 해설 (선택)
            $table->boolean('is_active')->default(true); // 활성화 여부
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};
