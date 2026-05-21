<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('reportable'); // reportable_type, reportable_id
            $table->string('reason');     // spam | abuse | obscene | illegal | other
            $table->text('description')->nullable();
            $table->string('status')->default('pending'); // pending | resolved | dismissed
            $table->text('admin_note')->nullable();
            $table->timestamps();

            // 한 유저는 같은 대상에 한 번만 신고 가능
            $table->unique(['user_id', 'reportable_type', 'reportable_id'], 'reports_user_reportable_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
