<?php

namespace App\Filament\Resources\Reservations\Schemas;

use App\Models\Reservation;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

/**
 * Rezervasyon detay görünümü — 3 sekme: Özet / Konaklama / Sistem.
 * Filament 4 Infolist Tabs API. "Geçmiş" (Activity Log) ayrı tab olarak
 * ResourceMain'de RelationManager üzerinden geliyor (ActivitiesRelationManager).
 *
 * Önceki 5 ayrı Section yerleşim dağınıktı; tabs sayesinde:
 * 1. Özet: misafir + rezervasyon kodu + durum + oda (en çok bakılan)
 * 2. Konaklama: tarih/kişi/tutar + notlar (uzun bilgi grubu)
 * 3. Sistem: oluşturulma/güncelleme zamanları (collapsed minimal)
 */
class ReservationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Rezervasyon')
                    ->columnSpanFull()
                    ->persistTabInQueryString('tab')
                    ->tabs([
                        // ─────────── Tab 1: Özet ───────────
                        Tab::make('Özet')
                            ->icon(Heroicon::OutlinedClipboardDocumentList)
                            ->schema([
                                Section::make('Rezervasyon')
                                    ->icon(Heroicon::OutlinedHashtag)
                                    ->iconColor('primary')
                                    ->columns(3)
                                    ->schema([
                                        TextEntry::make('reservation_code')
                                            ->label('Kod')
                                            ->copyable()
                                            ->copyMessage('Kod kopyalandı')
                                            ->weight('bold')
                                            ->size(TextSize::Large)
                                            ->color('primary'),

                                        TextEntry::make('status')
                                            ->label('Durum')
                                            ->badge()
                                            ->size(TextSize::Large),

                                        TextEntry::make('room.name')
                                            ->label('Oda')
                                            ->icon(Heroicon::OutlinedHome)
                                            ->iconColor('gray')
                                            ->weight('semibold'),
                                    ]),

                                Section::make('Misafir İletişim')
                                    ->icon(Heroicon::OutlinedUserCircle)
                                    ->iconColor('primary')
                                    ->columns(3)
                                    ->schema([
                                        TextEntry::make('guest_full_name')
                                            ->label('Ad Soyad')
                                            ->state(fn (Reservation $record) => $record->guest_full_name)
                                            ->icon(Heroicon::OutlinedUser)
                                            ->iconColor('gray')
                                            ->weight('semibold'),

                                        TextEntry::make('guest_phone')
                                            ->label('Telefon')
                                            ->icon(Heroicon::OutlinedPhone)
                                            ->iconColor('success')
                                            ->copyable()
                                            ->copyMessage('Telefon kopyalandı')
                                            ->url(fn (Reservation $record) => $record->whatsapp_link)
                                            ->openUrlInNewTab()
                                            ->helperText('Tıkla → WhatsApp aç'),

                                        TextEntry::make('guest_email')
                                            ->label('E-posta')
                                            ->icon(Heroicon::OutlinedEnvelope)
                                            ->iconColor('info')
                                            ->copyable()
                                            ->url(fn (Reservation $record) => 'mailto:'.$record->guest_email),
                                    ]),
                            ]),

                        // ─────────── Tab 2: Konaklama ───────────
                        Tab::make('Konaklama')
                            ->icon(Heroicon::OutlinedMoon)
                            ->schema([
                                Section::make('Tarih & Tutar')
                                    ->icon(Heroicon::OutlinedCalendarDays)
                                    ->iconColor('primary')
                                    ->columns(3)
                                    ->schema([
                                        TextEntry::make('check_in')
                                            ->label('Giriş')
                                            ->date('d.m.Y · l')
                                            ->icon(Heroicon::OutlinedArrowRightOnRectangle)
                                            ->iconColor('success')
                                            ->weight('semibold'),

                                        TextEntry::make('check_out')
                                            ->label('Çıkış')
                                            ->date('d.m.Y · l')
                                            ->icon(Heroicon::OutlinedArrowLeftOnRectangle)
                                            ->iconColor('info')
                                            ->weight('semibold'),

                                        TextEntry::make('nights')
                                            ->label('Gece Sayısı')
                                            ->numeric()
                                            ->suffix(' gece')
                                            ->weight('semibold'),

                                        TextEntry::make('adults')
                                            ->label('Yetişkin')
                                            ->numeric()
                                            ->icon(Heroicon::OutlinedUser),

                                        TextEntry::make('children')
                                            ->label('Çocuk')
                                            ->numeric()
                                            ->icon(Heroicon::OutlinedUserGroup),

                                        TextEntry::make('total_price')
                                            ->label('Toplam Tutar')
                                            ->money('TRY')
                                            ->size(TextSize::Large)
                                            ->weight('bold')
                                            ->color('primary'),
                                    ]),

                                Section::make('Notlar')
                                    ->icon(Heroicon::OutlinedChatBubbleBottomCenterText)
                                    ->iconColor('gray')
                                    ->columns(2)
                                    ->schema([
                                        TextEntry::make('special_requests')
                                            ->label('Özel İstekler (Misafir)')
                                            ->placeholder('— Misafir not bırakmadı —')
                                            ->columnSpanFull(),

                                        TextEntry::make('admin_notes')
                                            ->label('Yönetici Notları (Sadece Admin)')
                                            ->placeholder('— Henüz not eklenmedi —')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ─────────── Tab 3: Sistem ───────────
                        Tab::make('Sistem')
                            ->icon(Heroicon::OutlinedCog)
                            ->schema([
                                Section::make('Kayıt Zamanları')
                                    ->icon(Heroicon::OutlinedClock)
                                    ->iconColor('gray')
                                    ->columns(2)
                                    ->schema([
                                        TextEntry::make('created_at')
                                            ->label('Oluşturulma')
                                            ->dateTime('d.m.Y H:i')
                                            ->placeholder('—'),

                                        TextEntry::make('updated_at')
                                            ->label('Son Güncelleme')
                                            ->dateTime('d.m.Y H:i')
                                            ->placeholder('—'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
