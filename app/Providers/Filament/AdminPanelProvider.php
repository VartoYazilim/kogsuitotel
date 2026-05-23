<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\LatestReservations;
use App\Filament\Widgets\ReservationsChart;
use App\Filament\Widgets\ReservationStatsOverview;
use App\Filament\Widgets\TodayActivity;
use App\Filament\Widgets\WelcomeWidget;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('kog')
            ->path('kog-yonetim')
            ->brandName('Koğ Suit Yönetim')
            ->brandLogo(asset('images/logo.svg'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('favicon.svg'))
            ->login()

            // ── Multi-Factor Authentication — App (TOTP / Google Authenticator) ──
            // Admin login sonrası MFA challenge → user secret yoksa setup'a yönlenir
            // (QR code göster, Google Authenticator/Authy ile tara, kodu doğrula,
            //  recovery code'ları sakla). Sonraki login'lerde kod ister.
            // KVKK m.12 ek güvenlik tedbiri + CLAUDE.md "prod öncesi zorunlu".
            ->multiFactorAuthentication([
                AppAuthentication::make()
                    ->recoverable(),
            ])
            ->colors([
                'primary' => [
                    50 => '#f5f6f1',
                    100 => '#e5e8dc',
                    200 => '#c4ccb4',
                    300 => '#a8b091',
                    400 => '#88936f',
                    500 => '#6b7553',
                    600 => '#566042',
                    700 => '#4a5240',
                    800 => '#3b4234',
                    900 => '#2d3527',
                    950 => '#1f2517',
                ],
                'gray' => Color::Stone,
                'success' => Color::hex('#5a8a5e'),
                'warning' => Color::hex('#c49a4d'),
                'danger' => Color::hex('#b14b3a'),
                'info' => Color::hex('#8b6f4e'),
            ])

            // ── Navigation groups (sol menü düzeni) ──
            ->navigationGroups([
                NavigationGroup::make('Operasyon')
                    ->icon(Heroicon::OutlinedBriefcase)
                    ->collapsible(false),
                NavigationGroup::make('İçerik')
                    ->icon(Heroicon::OutlinedPhoto)
                    ->collapsible(true),
                NavigationGroup::make('Sistem')
                    ->icon(Heroicon::OutlinedCog6Tooth)
                    ->collapsible(true)
                    ->collapsed(),
            ])

            // ── Custom panel theme — Filament panel Tailwind build'i bu CSS
            //    dosyasinı işler; @source directive'leri ile resources/views/filament
            //    ve app/Filament dosyalarındaki Tailwind class'ları scan eder.
            //    Olive Sanctuary tokens light + dark variant burada tanımlı.
            ->viteTheme('resources/css/filament/kog/theme.css')

            // ── Dark mode AÇIK — kullanıcı tarayıcı/sistem tercihini takip eder
            //    veya panel header'daki toggle ile manuel değişir. Olive Sanctuary
            //    light + dark variant theme.css'te koordineli tasarlandı.
            ->darkMode(true)

            // ── Sidebar — masaüstünde daraltılabilir ──
            ->sidebarCollapsibleOnDesktop()

            // ── SPA mode — sayfa değişimlerinde tam sayfa yenileme yok ──
            ->spa()

            // ── Database notifications — header'da bell ikonu ──
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')

            // ── User menu — siteye dön linki + profil ──
            ->userMenuItems([
                'site' => MenuItem::make()
                    ->label('Siteyi Aç')
                    ->url(fn (): string => url('/'))
                    ->icon(Heroicon::OutlinedGlobeAlt)
                    ->openUrlInNewTab(),
            ])

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                WelcomeWidget::class,
                ReservationStatsOverview::class,
                TodayActivity::class,
                ReservationsChart::class,
                LatestReservations::class,
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
