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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('current_points')->default(0)->after('password')->index();
            $table->foreignId('tier_id')->nullable()->after('current_points')->constrained('tiers');
            $table->boolean('is_banned')->default(false)->after('tier_id');
            $table->timestamp('last_login_at')->nullable()->after('is_banned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
