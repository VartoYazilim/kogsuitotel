<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Hotfix (2026-05-23): Laravel Cloud Postgres prod'unda Filament admin
     * panel `notifications.data->>'format'` JSON arrow sorgusu fail oluyor
     * (`SQLSTATE[42883] operator does not exist: text ->> unknown`).
     *
     * Sebep: Laravel default `notifications:table` migration `data` kolunu
     * TEXT olarak oluşturuyor. SQLite/MySQL esnek; PostgreSQL'de TEXT üzerinde
     * JSON arrow operatörü yok — kolun JSONB tipinde olması gerek.
     *
     * Bu migration sadece pgsql driver'da kolun tipini değiştirir, diğer
     * driver'larda (SQLite test, MySQL alternatif) no-op.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE notifications ALTER COLUMN data TYPE jsonb USING data::jsonb');
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE notifications ALTER COLUMN data TYPE text USING data::text');
        }
    }
};
