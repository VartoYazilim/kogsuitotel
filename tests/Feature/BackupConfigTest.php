<?php

namespace Tests\Feature;

use Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification;
use Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification;
use Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification;
use Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification;
use Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes;
use Tests\TestCase;

class BackupConfigTest extends TestCase
{
    public function test_r2_disk_filesystem_konfigure(): void
    {
        $r2 = config('filesystems.disks.r2');

        $this->assertNotNull($r2, 'R2 disk filesystems.php\'da tanımlı olmalı');
        $this->assertSame('s3', $r2['driver']);
        $this->assertSame('auto', $r2['region']);
        $this->assertTrue($r2['use_path_style_endpoint']);
        $this->assertSame('kogsuit-backup', $r2['bucket'] ?? 'kogsuit-backup');
    }

    public function test_backup_destination_r2_disk_kullanir(): void
    {
        $disks = config('backup.backup.destination.disks');

        $this->assertContains('r2', $disks, 'Backup R2 disk\'ine yazmalı');
        $this->assertNotContains('local', $disks, 'Local kalıcı backup yok — disk şişmesini önler');
    }

    public function test_backup_filename_prefix_kogsuit(): void
    {
        $prefix = config('backup.backup.destination.filename_prefix');
        $this->assertSame('kogsuit_', $prefix);
    }

    public function test_backup_mail_notifications_kapali_proje_karari(): void
    {
        // Mail kanalı kaldırıldı — sahip kararı, havale + WhatsApp akışı
        $notifications = config('backup.notifications.notifications');

        $this->assertSame([], $notifications[BackupHasFailedNotification::class]);
        $this->assertSame([], $notifications[BackupWasSuccessfulNotification::class]);
        $this->assertSame([], $notifications[CleanupHasFailedNotification::class]);
        $this->assertSame([], $notifications[UnhealthyBackupWasFoundNotification::class]);
    }

    public function test_backup_monitor_r2_disk_kontrol_eder(): void
    {
        $monitors = config('backup.monitor_backups');
        $this->assertNotEmpty($monitors);
        $this->assertContains('r2', $monitors[0]['disks']);
    }

    public function test_backup_monitor_health_check_5gb_cap(): void
    {
        // R2 free tier 10GB; 5GB cap aşılırsa unhealthy alarm
        $checks = config('backup.monitor_backups.0.health_checks');
        $this->assertSame(5000, $checks[MaximumStorageInMegabytes::class]);
    }

    public function test_backup_cleanup_5gb_cap(): void
    {
        // Cleanup strategy 5GB üstüne çıkmasın
        $cap = config('backup.cleanup.default_strategy.delete_oldest_backups_when_using_more_megabytes_than');
        $this->assertSame(5000, $cap);
    }

    public function test_backup_retention_policy_makul(): void
    {
        $strategy = config('backup.cleanup.default_strategy');
        // 7+16 günlük + 8 haftalık + 4 aylık + 2 yıllık (~21 dosya max)
        $this->assertSame(7, $strategy['keep_all_backups_for_days']);
        $this->assertSame(16, $strategy['keep_daily_backups_for_days']);
        $this->assertSame(8, $strategy['keep_weekly_backups_for_weeks']);
        $this->assertSame(4, $strategy['keep_monthly_backups_for_months']);
        $this->assertSame(2, $strategy['keep_yearly_backups_for_years']);
    }

    public function test_backup_archive_encryption_default_aes(): void
    {
        // BACKUP_ARCHIVE_PASSWORD set edilirse AES-256 ile şifrelenir
        $algo = config('backup.backup.encryption');
        $this->assertSame('default', $algo); // 'default' = AES-256 available
    }
}
