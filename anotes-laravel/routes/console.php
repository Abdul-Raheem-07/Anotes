<?php

use App\Console\Commands\CleanupTrash;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Trash auto-cleanup: mirrors the legacy db.php behavior that permanently
// removes soft-deleted Notes and Reminders older than 7 days.
// Runs daily via Laravel Scheduler. In a local XAMPP environment without a
// server cron, run manually: php artisan trash:cleanup
Schedule::command('trash:cleanup')->daily();
