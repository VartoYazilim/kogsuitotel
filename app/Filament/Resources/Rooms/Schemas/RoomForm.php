<?php

namespace App\Filament\Resources\Rooms\Schemas;

use App\Services\ImageWebpConverter;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

/**
 * Oda form'u — 3 sekme: Temel / Özellikler / Görseller.
 * Filament 4 Tabs API. Dağınık alt-alta section'lar yerine ufuk grupları:
 * 1. Temel: ad/slug/açıklama/kapasite/fiyat/sıra/aktif — hepsi tek görünümde
 * 2. Özellikler: tag input (Wi-Fi, Klima, vs.)
 * 3. Görseller: kapak + galeri grid (mevcut görseller preview)
 */
class RoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Oda Bilgileri')
                    ->columnSpanFull()
                    ->persistTabInQueryString('tab')
                    ->tabs([
                        // ─────────── Tab 1: Temel ───────────
                        Tab::make('Temel')
                            ->icon(Heroicon::OutlinedDocumentText)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Oda Adı')
                                    ->required()
                                    ->maxLength(120)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $state, callable $set, callable $get) {
                                        if (empty($get('slug'))) {
                                            $set('slug', Str::slug($state));
                                        }
                                    })
                                    ->columnSpan(2),

                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->helperText('Pasif odalar site genelinde gizlenir.')
                                    ->default(true)
                                    ->inline(false)
                                    ->columnSpan(1),

                                TextInput::make('slug')
                                    ->label('URL Kısaltması')
                                    ->required()
                                    ->maxLength(120)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Örnek: deluxe-suit')
                                    ->columnSpan(2),

                                TextInput::make('sort_order')
                                    ->label('Sıralama')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Küçük değer üstte')
                                    ->columnSpan(1),

                                Textarea::make('description')
                                    ->label('Açıklama')
                                    ->required()
                                    ->rows(3)
                                    ->columnSpanFull(),

                                TextInput::make('capacity')
                                    ->label('Kapasite (kişi)')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(2)
                                    ->prefixIcon(Heroicon::OutlinedUserGroup)
                                    ->columnSpan(1),

                                TextInput::make('base_price')
                                    ->label('Gecelik Fiyat')
                                    ->required()
                                    ->numeric()
                                    ->prefix('₺')
                                    ->step(0.01)
                                    ->columnSpan(2),
                            ])
                            ->columns(3),

                        // ─────────── Tab 2: Özellikler ───────────
                        Tab::make('Özellikler')
                            ->icon(Heroicon::OutlinedSparkles)
                            ->schema([
                                TagsInput::make('features')
                                    ->label('Oda Özellikleri')
                                    ->placeholder('Yeni özellik ekle ve Enter\'a bas')
                                    ->helperText('Örnek: Wi-Fi, Klima, Smart TV, Jakuzi, Mini Bar')
                                    ->columnSpanFull(),
                            ]),

                        // ─────────── Tab 3: Görseller ───────────
                        Tab::make('Görseller')
                            ->icon(Heroicon::OutlinedPhoto)
                            ->schema([
                                FileUpload::make('cover_image')
                                    ->label('Kapak Görseli')
                                    ->image()
                                    ->disk('public')
                                    ->directory('rooms/covers')
                                    ->imageEditor()
                                    ->maxSize(20480)
                                    ->panelLayout('integrated')
                                    ->imagePreviewHeight('200')
                                    ->panelAspectRatio('16:9')
                                    ->helperText('Tek görsel. Ana site listelemesinde görünür. JPG/PNG → otomatik WebP.')
                                    ->saveUploadedFileUsing(fn ($file) => app(ImageWebpConverter::class)->convert($file, 'rooms/covers'))
                                    ->columnSpan(1),

                                FileUpload::make('gallery')
                                    ->label('Galeri Görselleri')
                                    ->image()
                                    ->multiple()
                                    ->disk('public')
                                    ->directory('rooms/gallery')
                                    ->reorderable()
                                    ->appendFiles()
                                    ->maxSize(20480)
                                    ->maxFiles(20)
                                    ->panelLayout('grid')
                                    ->imagePreviewHeight('140')
                                    ->panelAspectRatio('4:3')
                                    ->helperText('Mevcut görseller aşağıda; X ile sil, sürükle ile sırala. Yeni yüklemeler eklenir. Maks. 20.')
                                    ->saveUploadedFileUsing(fn ($file) => app(ImageWebpConverter::class)->convert($file, 'rooms/gallery'))
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }
}
