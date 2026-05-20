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
            $table->string('usetime')->nullable()->after('overview');        // 이용시간
            $table->string('restdate')->nullable()->after('usetime');        // 휴무일
            $table->string('usefee')->nullable()->after('restdate');         // 이용요금
            $table->text('parking')->nullable()->after('usefee');            // 주차 정보
            $table->string('chkpet')->nullable()->after('parking');          // 반려동물
            $table->string('chkbabycarriage')->nullable()->after('chkpet'); // 유모차
            $table->json('extra_images')->nullable()->after('chkbabycarriage'); // 추가 이미지
        });
    }

    public function down(): void
    {
        Schema::table('tourist_spots', function (Blueprint $table) {
            $table->dropColumn([
                'usetime', 'restdate', 'usefee', 'parking',
                'chkpet', 'chkbabycarriage', 'extra_images',
            ]);
        });
    }
};
