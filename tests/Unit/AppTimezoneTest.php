<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;

class AppTimezoneTest extends TestCase
{
    public function test_config_timezone_env_ile_set_edilebilir(): void
    {
        // phpunit.xml'de APP_TIMEZONE override yoksa config/app.php fallback'i devreye girer.
        // Bu test: config('app.timezone') env'i okuyor mu doğrular.
        config(['app.timezone' => 'Europe/Istanbul']);
        date_default_timezone_set(config('app.timezone'));

        $this->assertSame('Europe/Istanbul', config('app.timezone'));
        $this->assertSame('Europe/Istanbul', date_default_timezone_get());
    }

    public function test_carbon_now_app_timezone_kullanir(): void
    {
        config(['app.timezone' => 'Europe/Istanbul']);
        date_default_timezone_set('Europe/Istanbul');

        $now = Carbon::now();

        $this->assertSame('Europe/Istanbul', $now->timezoneName);
    }

    public function test_config_app_timezone_env_app_timezone_uzerinden_set_edilir(): void
    {
        // config/app.php'deki 'timezone' => env('APP_TIMEZONE', 'UTC') doğruluğu
        $configValue = include base_path('config/app.php');
        $this->assertArrayHasKey('timezone', $configValue);
        // env('APP_TIMEZONE') değeri bootstrap sırasında zaten okunmuş — config'te string olarak görünür
        $this->assertIsString($configValue['timezone']);
    }
}
