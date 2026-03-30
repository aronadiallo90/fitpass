<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cron quotidien : expirer les abonnements + envoyer les rappels SMS
Schedule::command('fitpass:expire-subscriptions')->dailyAt('02:00');
