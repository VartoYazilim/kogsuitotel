<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\Settings\Pages\ManageSettings;
use App\Models\Setting;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $recordTitleAttribute = 'key';

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 90;

    /**
     * İşletme anahtarlarının Türkçe karşılıkları + grupları.
     * Yeni anahtar eklemek istenirse buraya ekleyin, otomatik formda label olur.
     */
    public const KEY_LABELS = [
        'iban' => 'IBAN',
        'iban_holder' => 'Hesap Sahibi',
        'bank_name' => 'Banka Adı',
        'phone' => 'Telefon',
        'whatsapp' => 'WhatsApp',
        'email' => 'E-posta',
        'address' => 'Adres',
        'checkin_time' => 'Check-in Saati',
        'checkout_time' => 'Check-out Saati',
        'instagram_url' => 'Instagram URL',
        'facebook_url' => 'Facebook URL',
        'google_maps_url' => 'Google Maps URL',
        'tripadvisor_url' => 'Tripadvisor URL',
    ];

    public static function getNavigationLabel(): string
    {
        return 'Ayarlar';
    }

    /**
     * Custom sıralama: kategori önce, sonra key.
     * Filament'in defaultSort virtual attribute desteklemediği için SQL CASE ile.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderByRaw("
            CASE
                WHEN key IN ('iban', 'iban_holder', 'bank_name') THEN 1
                WHEN key IN ('phone', 'whatsapp', 'email', 'address') THEN 2
                WHEN key IN ('checkin_time', 'checkout_time') THEN 3
                WHEN key LIKE '%_url' THEN 4
                ELSE 5
            END,
            key ASC
        ");
    }

    public static function getModelLabel(): string
    {
        return 'Ayar';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Ayarlar';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->label('Anahtar')
                    ->required()
                    ->maxLength(80)
                    ->unique(ignoreRecord: true)
                    ->disabledOn('edit')
                    ->helperText('Programatik anahtar (örn: iban, phone). Oluşturulduktan sonra değiştirilemez.'),
                Textarea::make('value')
                    ->label('Değer')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Setting key prefix'inden kategori adı türet — tabloda gruplandırma için.
     */
    protected static function categoryForKey(string $key): string
    {
        return match (true) {
            in_array($key, ['iban', 'iban_holder', 'bank_name'], true) => '💰 Banka',
            in_array($key, ['phone', 'whatsapp', 'email', 'address'], true) => '📞 İletişim',
            in_array($key, ['checkin_time', 'checkout_time'], true) => '🛏 Konaklama',
            str_ends_with($key, '_url') => '🌐 Sosyal Medya',
            default => '⚙ Diğer',
        };
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('key')
            ->defaultSort('key')
            ->columns([
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->state(fn ($record) => self::categoryForKey($record->key))
                    ->color(fn (string $state) => match (true) {
                        str_contains($state, 'Banka') => 'success',
                        str_contains($state, 'İletişim') => 'info',
                        str_contains($state, 'Konaklama') => 'warning',
                        str_contains($state, 'Sosyal') => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('key')
                    ->label('Anahtar')
                    ->color('gray')
                    ->fontFamily('mono')
                    ->searchable(),
                TextColumn::make('label')
                    ->label('Açıklama')
                    ->state(fn ($record) => self::KEY_LABELS[$record->key] ?? '—')
                    ->color('gray'),
                TextColumn::make('value')
                    ->label('Değer')
                    ->formatStateUsing(fn (?string $state) => $state === '' || $state === null ? '—' : $state)
                    ->limit(60)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('updated_at')
                    ->label('Güncelleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            // NOT: `->groups([Group::make('category')])` + `defaultGroup('category')`
            // Filament'i `ORDER BY category` SQL'i üretmeye zorluyor. SQLite/MySQL
            // virtual alias tolere eder, PostgreSQL strict — "column does not exist"
            // hatası verir. Visual grouping zaten Category badge ile sağlanıyor +
            // getEloquentQuery() CASE WHEN ile kategori-sıralı geliyor, accordion
            // kaybedildi ama 13 setting küçük — scroll sorun değil. Gerçek group
            // istenirse Filament 4 `orderQueryUsing(closure)` ile geri eklenebilir.
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSettings::route('/'),
        ];
    }
}
