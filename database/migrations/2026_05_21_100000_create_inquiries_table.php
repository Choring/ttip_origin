<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');                        // 작성자 이름
            $table->string('email');                       // 연락 이메일
            $table->string('subject');                     // 문의 제목
            $table->text('content');                       // 문의 내용
            $table->enum('status', ['pending', 'answered'])->default('pending'); // 상태
            $table->text('answer')->nullable();            // 관리자 답변
            $table->timestamp('answered_at')->nullable();  // 답변 일시
            $table->foreignId('answered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
