<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('terms_agreed_at')->nullable()->after('remember_token');
            $table->timestamp('privacy_agreed_at')->nullable()->after('terms_agreed_at');
            $table->string('agreed_ip', 45)->nullable()->after('privacy_agreed_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['terms_agreed_at', 'privacy_agreed_at', 'agreed_ip']);
        });
    }
};
