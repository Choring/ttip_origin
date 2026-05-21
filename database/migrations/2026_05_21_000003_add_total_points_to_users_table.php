<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('total_points')->default(0)->after('current_points');
        });

        // 기존 유저: current_points 값을 total_points 초기값으로 복사
        DB::statement('UPDATE users SET total_points = current_points');
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('total_points');
        });
    }
};
