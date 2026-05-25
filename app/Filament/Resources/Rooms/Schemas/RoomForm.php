<?php

namespace App\Filament\Resources\Rooms\Schemas;

use App\Services\ImageWebpConverter;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class RoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ─────────── 1. Temel Bilgiler (ad + slug + açıklama + aktif) ───────────
                Section::make('Temel Bilgiler')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->iconColor('primary')
                    ->columns(3)
                    ->components([
                        TextInput::make('name')
                            ->label('Oda Adı')
                            ->required()
                            ->maxLength(120)
                            ->columnSpan(2)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $state, callable $set, callable $get) {
                                if (empty($get('slug'))) {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->helperText('Pasif odalar site genelinde gizlenir.')
                            ->default(true)
                            ->columnSpan(1)
                            ->inline(false),

                        TextInput::make('slug')
                            ->label('URL Kısaltması')
                            ->required()
                            ->maxLength(120)
                            ->unique(ignoreRecord: true)
                            ->helperText('Örnek: deluxe-suit')
                            ->columnSpan(2),

                        Textarea::make('description')
                            ->label('Açıklama')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                // ─────────── 2. Kapasite & Fiyat ───────────
                Section::make('Kapasite & Fiyat')
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->iconColor('primary')
                    ->columns(3)
                    ->components([
                        TextInput::make('capacity')
                            ->label('Kapasite (kişi)')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(2)
                            ->prefixIcon(Heroicon::OutlinedUserGroup),

                        TextInput::make('base_price')
                            ->label('Gecelik Fiyat')
                            ->required()
                            ->numeric()
                            ->prefix('₺')
                            ->step(0.01),

                        TextInput::make('sort_order')
                            ->label('Sıralama')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Küçük değer üstte'),
                    ]),

                // ─────────── 3. Özellikler ───────────
                Section::make('Özellikler')
                    ->icon(Heroicon::OutlinedSparkles)
                    ->iconColor('primary')
                    ->components([
                        TagsInput::make('features')
                            ->label('Oda Özellikleri')
                            ->placeholder('Yeni özellik ekle ve Enter\'a bas')
                            ->helperText('Örnek: Wi-Fi, Klima, Smart TV, Jakuzi, Mini Bar')
                            ->columnSpanFull(),
                    ]),

                // ─────────── 4. Görseller (kapak + galeri grid) ───────────
                Section::make('Görseller')
                    ->icon(Heroicon::OutlinedPhoto)
                    ->iconColor('primary')
                    ->description('Kapak görseli ana foto. Galeri detay sayfasında çoklu görsel olarak görünür.')
                    ->columns(3)
                    ->components([
                        FileUpload::make('cover_image')
                            ->label('Kapak Görseli')
                            ->image()
                            ->disk('public')
                            ->directory('rooms/covers')
                            ->imageEditor()
                            ->maxSize(20480)
                            ->panelLayout('integrated')
                            ->imagePreviewHeight('160')
                            ->panelAspectRatio('16:9')
                            ->helperText('Tek görsel. JPG/PNG → otomatik WebP.')
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
                            ->imagePreviewHeight('120')
                            ->panelAspectRatio('4:3')
                            ->helperText('Birden çok foto. Sürükleyerek sırala, X ile sil. Yeni yüklemeler mevcuta eklenir. Maks. 20.')
                            ->saveUploadedFileUsing(fn ($file) => app(ImageWebpConverter::class)->convert($file, 'rooms/gallery'))
                            ->columnSpan(2),
                    ]),
            ]);
    }
}
