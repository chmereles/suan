<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('attendance:sync-crosschex')
    ->everyTenMinutes()
    ->withoutOverlapping()
    ->onOneServer(); // si algún día tenés varios nodos
