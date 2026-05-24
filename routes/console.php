<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks — Spatie Laravel Backup
|--------------------------------------------------------------------------
| Cron runs every minute via VPS crontab:
|   * * * * * cd /var/www/kogsuitotel && php artisan schedule:run >> /dev/null 2>&1
|
| Cloudflare R2 disk (S3-uyumlu) backup hedefi. Local geçici temp_directory'de
| oluşturulur, R2'ye upload sonra silinir (75GB VPS disk şişmesini önler).
| Retention: 7 daily + 16 daily + 8 weekly + 4 monthly + 2 yearly,
| 5GB cap ile R2 free tier (10GB) altında kalır.
*/

// Günlük tam yedek 03:00 — DB + storage uploads + app dosyaları
Schedule::command('backup:run')
    ->dailyAt('03:00')
    ->timezone('Europe/Istanbul')
    ->onOneServer() // race condition koruması (multi-VPS senaryosu)
    ->withoutOverlapping()
    ->runInBackground();

// 04:00 — eski yedekleri retention strategy ile temizle
Schedule::command('backup:clean')
    ->dailyAt('04:00')
    ->timezone('Europe/Istanbul')
    ->onOneServer()
    ->withoutOverlapping();

// 04:30 — backup sağlık kontrolü (son backup 36 saat içinde + 5GB altında)
// Başarısızlık storage/logs/laravel.log'a yazılır (mail yok)
Schedule::command('backup:monitor')
    ->dailyAt('04:30')
    ->timezone('Europe/Istanbul');

/*
|--------------------------------------------------------------------------
| Disk Şişme Koruması
|--------------------------------------------------------------------------
| VPS 75GB disk; > %80 doluluk uyarısı laravel.log'a yazılır.
| Saatlik kontrol — bash cron olmadan Laravel scheduler içinde yapılır.
*/
Schedule::call(function () {
    $totalBytes = disk_total_space('/');
    $freeBytes = disk_free_space('/');
    $usedBytes = $totalBytes - $freeBytes;
    $usagePercent = $totalBytes > 0 ? round(($usedBytes / $totalBytes) * 100, 1) : 0;

    if ($usagePercent >= 80) {
        Log::warning('Disk doluluk %80\'i aştı', [
            'usage_percent' => $usagePercent,
            'used_gb' => round($usedBytes / 1024 / 1024 / 1024, 1),
            'free_gb' => round($freeBytes / 1024 / 1024 / 1024, 1),
            'total_gb' => round($totalBytes / 1024 / 1024 / 1024, 1),
        ]);
    }
})->hourly()->name('disk-usage-monitor');
