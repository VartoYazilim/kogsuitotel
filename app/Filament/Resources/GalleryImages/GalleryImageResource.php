<?php

namespace App\Filament\Resources\GalleryImages;

use App\Filament\Resources\GalleryImages\Pages\ManageGalleryImages;
use App\Models\GalleryImage;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class GalleryImageResource extends Resource
{
    protected static ?string $model = GalleryImage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $recordTitleAttribute = 'alt_text';

    protected static string|UnitEnum|null $navigationGroup = 'İçerik';

    protected static ?int $navigationSort = 30;

    /** Kategori sözlüğü — site içinde filtre olarak kullanılan kategori adları. */
    public const CATEGORIES = [
        'exterior' => 'Dış Mekan',
        'rooms' => 'Odalar',
        'lobby' => 'Lobi',
        'view' => 'Manzara',
        'bath' => 'Banyo',
        'breakfast' => 'Kahvaltı',
        'detail' => 'Detay',
    ];

    public static function getNavigationLabel(): string
    {
        return 'Galeri Görselleri';
    }

    public static function getModelLabel(): string
    {
        return 'Görsel';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Galeri Görselleri';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category')
                    ->label('Kategori')
                    ->options(self::CATEGORIES)
                    ->required()
                    ->native(false),
                FileUpload::make('path')
                    ->label('Görsel Dosyası')
                    ->image()
                    ->required()
                    ->disk('public')
                    ->directory('gallery')
                    ->imageEditor()
                    ->maxSize(5120),
                TextInput::make('alt_text')
                    ->label('Alternatif Metin (alt)')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Görseli açıklayan kısa metin. SEO ve erişilebilirlik için.'),
                TextInput::make('sort_order')
                    ->label('Sıralama')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('alt_text')
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('path')
                    ->label('Görsel')
                    ->getStateUsing(fn (GalleryImage $record): ?string => $record->path_url)
                    ->square()
                    ->width(80)
                    ->height(80),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->formatStateUsing(fn (?string $state) => self::CATEGORIES[$state] ?? $state)
                    ->badge()
                    ->sortable(),
                TextColumn::make('alt_text')
                    ->label('Açıklama')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->numeric()
                    ->alignCenter()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Güncelleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(self::CATEGORIES),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageGalleryImages::route('/'),
        ];
    }
}
