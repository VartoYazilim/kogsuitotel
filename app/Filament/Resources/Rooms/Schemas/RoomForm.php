<?php

namespace App\Filament\Resources\Rooms\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class RoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Temel Bilgiler')
                    ->columns(2)
                    ->components([
                        TextInput::make('name')
                            ->label('Oda Adı')
                            ->required()
                            ->maxLength(120)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $state, callable $set, callable $get) {
                                if (empty($get('slug'))) {
                                    $set('slug', Str::slug($state));
                                }
                            }),
                        TextInput::make('slug')
                            ->label('URL Kısaltması (slug)')
                            ->required()
                            ->maxLength(120)
                            ->unique(ignoreRecord: true)
                            ->helperText('Örnek: deluxe-suit'),
                        Textarea::make('description')
                            ->label('Açıklama')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Kapasite & Fiyat')
                    ->columns(3)
                    ->components([
                        TextInput::make('capacity')
                            ->label('Kapasite (kişi)')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(2),
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
                            ->helperText('Küçük değer üstte görünür.'),
                    ]),

                Section::make('Özellikler')
                    ->components([
                        TagsInput::make('features')
                            ->label('Oda Özellikleri')
                            ->placeholder('Yeni özellik ekleyin')
                            ->helperText('Örnek: Wi-Fi, Klima, Smart TV, Jakuzi')
                            ->columnSpanFull(),
                    ]),

                Section::make('Görseller')
                    ->components([
                        FileUpload::make('cover_image')
                            ->label('Kapak Görseli')
                            ->image()
                            ->disk('public')
                            ->directory('rooms/covers')
                            ->imageEditor()
                            ->maxSize(5120)
                            ->columnSpanFull(),
                        FileUpload::make('gallery')
                            ->label('Galeri Görselleri')
                            ->image()
                            ->multiple()
                            ->disk('public')
                            ->directory('rooms/gallery')
                            ->reorderable()
                            ->maxSize(5120)
                            ->maxFiles(20)
                            ->columnSpanFull(),
                    ]),

                Section::make('Durum')
                    ->components([
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->helperText('Pasif yapılırsa site genelinde gizlenir.')
                            ->default(true),
                    ]),
            ]);
    }
}
