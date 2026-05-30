<?php

namespace App\Filament\Resources\LegalPages;

use App\Filament\Resources\LegalPages\Pages\EditLegalPage;
use App\Filament\Resources\LegalPages\Pages\ListLegalPages;
use App\Models\LegalPage;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Hukuki sayfa yönetimi — KVKK / Gizlilik / Çerez Politikası 3 sabit kayıt.
 * Sadece düzenleme (create/delete kapalı — slug'lar sabit, route'lara bağlı).
 *
 * UYARI: Hukuki metinler avukat onayı sonrası güncellenmeli; Activity log
 * tüm değişimleri kaydeder (KVKK m.12/3 denetim trail).
 */
class LegalPageResource extends Resource
{
    protected static ?string $model = LegalPage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|UnitEnum|null $navigationGroup = 'İçerik';

    protected static ?int $navigationSort = 50;

    public static function getNavigationLabel(): string
    {
        return 'Hukuki Sayfalar';
    }

    public static function getModelLabel(): string
    {
        return 'Hukuki Sayfa';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Hukuki Sayfalar';
    }

    /** Yeni sayfa eklenemez — 3 slug sabit, route'lara bağlı. */
    public static function canCreate(): bool
    {
        return false;
    }

    /** Silinemez — slug sabit, silme hukuki risk. */
    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Başlık ve Tarih')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->iconColor('primary')
                    ->columns(2)
                    ->components([
                        TextInput::make('title')
                            ->label('Başlık')
                            ->required()
                            ->maxLength(200)
                            ->columnSpan(2),

                        TextInput::make('slug')
                            ->label('URL Slug')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Route\'a bağlı, değiştirilemez.'),

                        DatePicker::make('last_reviewed_at')
                            ->label('Son Gözden Geçirme')
                            ->native(false)
                            ->displayFormat('d.m.Y')
                            ->locale('tr')
                            ->helperText('Public sayfada "Son güncelleme: ..." olarak görünür.'),
                    ]),

                Section::make('İçerik')
                    ->icon(Heroicon::OutlinedBookOpen)
                    ->iconColor('primary')
                    ->components([
                        RichEditor::make('content_html')
                            ->label('Metin')
                            ->required()
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike',
                                'h2', 'h3',
                                'bulletList', 'orderedList',
                                'link', 'blockquote',
                                'undo', 'redo',
                            ])
                            ->helperText('Placeholder değişkenler: {{ phone }}, {{ email }}, {{ address }}, {{ last_reviewed_at }} — public sayfada otomatik dolar.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('slug')
            ->columns([
                TextColumn::make('title')
                    ->label('Sayfa')
                    ->weight('semibold')
                    ->searchable(),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state) => '/'.($state ?? '')),

                TextColumn::make('last_reviewed_at')
                    ->label('Son Gözden Geçirme')
                    ->date('d.m.Y')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Son Düzenleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make()->label('Düzenle'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLegalPages::route('/'),
            'edit' => EditLegalPage::route('/{record}/edit'),
        ];
    }
}
