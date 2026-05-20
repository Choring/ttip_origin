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
        Schema::table('tourist_spots', function (Blueprint $table) {
            $table->text('usetime')->nullable()->change();
            $table->text('restdate')->nullable()->change();
            $table->text('usefee')->nullable()->change();
            $table->text('chkpet')->nullable()->change();
            $table->text('chkbabycarriage')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tourist_spots', function (Blueprint $table) {
            $table->string('usetime')->nullable()->change();
            $table->string('restdate')->nullable()->change();
            $table->string('usefee')->nullable()->change();
            $table->string('chkpet')->nullable()->change();
            $table->string('chkbabycarriage')->nullable()->change();
        });
    }
};
