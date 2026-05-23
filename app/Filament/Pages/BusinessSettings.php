<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * İşletme ayarları tek sayfa, kategorize Section'lar — IBAN/İletişim/
 * Konaklama/Sosyal Medya. Eski SettingResource (tablo + tek tek modal)
 * yerine geçer; sahibin her ayarı modal açmadan görüp toplu kaydeder.
 *
 * Setting::get() 5 dk cache'li okuma, Setting::set() yazımda cache invalidate
 * eder — bu Page batch save'de set() döngüsü güvenle çalışır.
 *
 * @property-read Schema $form
 */
class BusinessSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.business-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 90;

    /** @var list<string> Form alanları ↔ DB key'leri eşleşmesi. */
    public const KEYS = [
        'iban',
        'iban_holder',
        'bank_name',
        'phone',
        'whatsapp',
        'email',
        'address',
        'checkin_time',
        'checkout_time',
        'instagram_url',
        'facebook_url',
        'google_maps_url',
        'tripadvisor_url',
    ];

    public ?array $data = [];

    public function getTitle(): string
    {
        return 'İşletme Ayarları';
    }

    public function getHeading(): string
    {
        return 'İşletme Ayarları';
    }

    public function getSubheading(): ?string
    {
        return 'IBAN, iletişim, konaklama saatleri ve sosyal medya bağlantıları tek yerden.';
    }

    public static function getNavigationLabel(): string
    {
        return 'Ayarlar';
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'ayarlar';
    }

    public function mount(): void
    {
        $this->form->fill(Setting::many(self::KEYS));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Banka Bilgileri')
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->description('Misafirler dekont için bu IBAN\'a havale yapar. Hesap sahibi adı IBAN ile birebir olmalı.')
                    ->columns(2)
                    ->components([
                        TextInput::make('iban')
                            ->label('IBAN')
                            ->required()
                            ->maxLength(34)
                            ->placeholder('TR00 0000 0000 0000 0000 0000 00')
                            ->helperText('TR ile başlayan 26 karakter. Boşlukla yazılabilir, sistem temizler.')
                            ->columnSpanFull(),
                        TextInput::make('iban_holder')
                            ->label('Hesap Sahibi')
                            ->required()
                            ->maxLength(120),
                        TextInput::make('bank_name')
                            ->label('Banka Adı')
                            ->maxLength(80)
                            ->placeholder('Ziraat Bankası, İş Bankası, …'),
                    ]),

                Section::make('İletişim')
                    ->icon(Heroicon::OutlinedPhone)
                    ->description('Site footer\'ında, rezervasyon onay sayfasında ve schema.org yapısal verisinde kullanılır.')
                    ->columns(2)
                    ->components([
                        TextInput::make('phone')
                            ->label('Telefon')
                            ->tel()
                            ->prefixIcon(Heroicon::OutlinedPhone)
                            ->placeholder('+90 555 123 45 67')
                            ->maxLength(40),
                        TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->tel()
                            ->prefixIcon(Heroicon::OutlinedChatBubbleLeftRight)
                            ->placeholder('+90 555 123 45 67')
                            ->maxLength(40)
                            ->helperText('Misafirlerin dekont gönderdiği numara. Telefonla aynı olabilir.'),
                        TextInput::make('email')
                            ->label('E-posta')
                            ->email()
                            ->prefixIcon(Heroicon::OutlinedEnvelope)
                            ->placeholder('info@kogsuitotel.com')
                            ->maxLength(120),
                        Textarea::make('address')
                            ->label('Adres')
                            ->rows(2)
                            ->maxLength(255)
                            ->placeholder('Varto, Muş, Türkiye')
                            ->columnSpanFull(),
                    ]),

                Section::make('Konaklama Saatleri')
                    ->icon(Heroicon::OutlinedClock)
                    ->description('SSS, oda detay ve rezervasyon başarı sayfasında otomatik gösterilir.')
                    ->columns(2)
                    ->components([
                        TimePicker::make('checkin_time')
                            ->label('Giriş Saati')
                            ->native(false)
                            ->seconds(false)
                            ->prefixIcon(Heroicon::OutlinedArrowRightOnRectangle)
                            ->placeholder('14:00'),
                        TimePicker::make('checkout_time')
                            ->label('Çıkış Saati')
                            ->native(false)
                            ->seconds(false)
                            ->prefixIcon(Heroicon::OutlinedArrowLeftOnRectangle)
                            ->placeholder('12:00'),
                    ]),

                Section::make('Sosyal Medya')
                    ->icon(Heroicon::OutlinedGlobeAlt)
                    ->description('Footer ve schema.org Organization linklerinde kullanılır. Boş bırakılan linkler gizlenir.')
                    ->columns(2)
                    ->collapsible()
                    ->components([
                        TextInput::make('instagram_url')
                            ->label('Instagram')
                            ->url()
                            ->prefixIcon(Heroicon::OutlinedCamera)
                            ->placeholder('https://instagram.com/kogsuitotel')
                            ->maxLength(255),
                        TextInput::make('facebook_url')
                            ->label('Facebook')
                            ->url()
                            ->prefixIcon(Heroicon::OutlinedUsers)
                            ->placeholder('https://facebook.com/kogsuitotel')
                            ->maxLength(255),
                        TextInput::make('google_maps_url')
                            ->label('Google Maps')
                            ->url()
                            ->prefixIcon(Heroicon::OutlinedMapPin)
                            ->placeholder('https://maps.google.com/?cid=…')
                            ->maxLength(255),
                        TextInput::make('tripadvisor_url')
                            ->label('Tripadvisor')
                            ->url()
                            ->prefixIcon(Heroicon::OutlinedStar)
                            ->placeholder('https://tripadvisor.com/Hotel_Review-…')
                            ->maxLength(255),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // IBAN'daki boşlukları temizle (sahibin yazım kolaylığı için kabul edilir)
        if (isset($data['iban'])) {
            $data['iban'] = preg_replace('/\s+/', '', (string) $data['iban']);
        }

        foreach (self::KEYS as $key) {
            // Sadece form'da olan key'leri yaz, eksik olanları geç (silinme değil)
            if (array_key_exists($key, $data)) {
                Setting::set($key, $data[$key] === null ? null : (string) $data[$key]);
            }
        }

        Notification::make()
            ->title('Ayarlar kaydedildi')
            ->body('İşletme bilgileri tüm sayfalarda anında güncellenir.')
            ->success()
            ->send();
    }
}
