<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:fetch-cultural-events')->dailyAt('03:00');
Schedule::command('app:fetch-kopis-events')->dailyAt('03:30');
Schedule::command('app:fetch-tourist-spots')->dailyAt('04:00');
Schedule::command('app:fetch-restaurants')->weeklyOn(1, '05:00'); // 매주 월요일
