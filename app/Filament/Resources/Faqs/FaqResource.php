<?php

namespace App\Filament\Resources\Faqs;

use App\Filament\Resources\Faqs\Pages\ManageFaqs;
use App\Models\Faq;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

/**
 * SSS yönetimi — admin'den CRUD, public /sss sayfası DB'den okur.
 * "İçerik" navigation group. Reorderable sort_order ile.
 */
class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static ?string $recordTitleAttribute = 'question';

    protected static string|UnitEnum|null $navigationGroup = 'İçerik';

    protected static ?int $navigationSort = 40;

    /** Default kategori sözlüğü — admin yeni kategori de yazabilir (custom serbest text). */
    public const CATEGORIES = [
        'Konaklama' => 'Konaklama',
        'Rezervasyon' => 'Rezervasyon',
        'Ödeme' => 'Ödeme',
        'Hizmetler' => 'Hizmetler',
        'Diğer' => 'Diğer',
    ];

    public static function getNavigationLabel(): string
    {
        return 'SSS';
    }

    public static function getModelLabel(): string
    {
        return 'Soru';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Sıkça Sorulan Sorular';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Soru & Cevap')
                    ->icon(Heroicon::OutlinedQuestionMarkCircle)
                    ->iconColor('primary')
                    ->columns(2)
                    ->components([
                        TextInput::make('question')
                            ->label('Soru')
                            ->required()
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Textarea::make('answer')
                            ->label('Cevap')
                            ->required()
                            ->rows(5)
                            ->maxLength(5000)
                            ->columnSpanFull(),

                        Select::make('category')
                            ->label('Kategori')
                            ->options(self::CATEGORIES)
                            ->placeholder('Seçin (opsiyonel)')
                            ->native(false)
                            ->searchable()
                            ->helperText('Public sayfada kategori bazlı filtre için.'),

                        TextInput::make('sort_order')
                            ->label('Sıralama')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Küçük değer üstte'),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Pasif sorular public sayfada gizlenir.')
                            ->inline(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('question')
                    ->label('Soru')
                    ->searchable()
                    ->wrap()
                    ->limit(80)
                    ->tooltip(fn (Faq $record) => $record->question),

                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->numeric()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

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

                TernaryFilter::make('is_active')
                    ->label('Durum')
                    ->placeholder('Tümü')
                    ->trueLabel('Aktif')
                    ->falseLabel('Pasif'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Henüz SSS yok')
            ->emptyStateDescription('İlk soruyu eklemek için üstteki "Yeni Soru" butonuna basın.')
            ->emptyStateIcon(Heroicon::OutlinedQuestionMarkCircle);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageFaqs::route('/'),
        ];
    }
}
