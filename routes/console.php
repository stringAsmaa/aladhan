<?php

use Illuminate\Foundation\Inspiring;
use App\Jobs\SendDailyZekrNotification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Schedule::job(new SendDailyZekrNotification('morning'))->dailyAt('06:00');

Schedule::job(new SendDailyZekrNotification('evening'))->dailyAt('00:045');
