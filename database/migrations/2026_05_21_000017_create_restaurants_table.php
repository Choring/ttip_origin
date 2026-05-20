<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->string('content_id')->primary();
            $table->string('title');
            $table->string('category')->default('기타');   // 한식/양식/일식/중식/카페/기타
            $table->string('address')->nullable();
            $table->string('image')->nullable();
            $table->string('homepage')->nullable();
            $table->string('tel')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
