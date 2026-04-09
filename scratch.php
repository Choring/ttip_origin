<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

if (\App\Models\User::count() < 3) {
    \App\Models\User::factory(3)->create();
}

$users = \App\Models\User::take(10)->get();
$scores = [12400, 10210, 9115];

foreach ($users as $k => $u) {
    $u->current_points = isset($scores[$k]) ? $scores[$k] : rand(100, 5000);
    $u->save();
}
echo "Points assigned successfully.\n";
