<?php

namespace App\Filament\Resources\SocialLinks;

use App\Filament\Resources\SocialLinks\Pages\ManageSocialLinks;
use App\Models\SocialLink;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Sosyal medya bağlantı yönetimi — dinamik (4 sabit Setting key'i değil).
 * Sahip istediği kadar platform ekler, sıralar, aktif/pasif yapar.
 * "İçerik" navigation group, sıralama drag-and-drop.
 */
class SocialLinkResource extends Resource
{
    protected static ?string $model = SocialLink::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static ?string $recordTitleAttribute = 'label';

    protected static string|UnitEnum|null $navigationGroup = 'İçerik';

    protected static ?int $navigationSort = 60;

    public static function getNavigationLabel(): string
    {
        return 'Sosyal Medya';
    }

    public static function getModelLabel(): string
    {
        return 'Sosyal Bağlantı';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Sosyal Bağlantılar';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Bağlantı')
                    ->icon(Heroicon::OutlinedGlobeAlt)
                    ->iconColor('primary')
                    ->columns(2)
                    ->components([
                        Select::make('platform')
                            ->label('Platform')
                            ->options(SocialLink::PLATFORMS)
                            ->required()
                            ->native(false)
                            ->searchable()
                            ->live()
                            ->helperText('İkon ve varsayılan etiket bu seçime göre ayarlanır.')
                            ->afterStateUpdated(function (?string $state, Set $set, $get) {
                                if ($state && empty($get('label'))) {
                                    $set('label', SocialLink::PLATFORMS[$state] ?? $state);
                                }
                            }),

                        TextInput::make('label')
                            ->label('Gösterilen Ad')
                            ->required()
                            ->maxLength(60)
                            ->helperText('Platform seçince otomatik dolar; özelleştirilebilir.'),

                        TextInput::make('url')
                            ->label('URL')
                            ->required()
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://...')
                            ->prefixIcon(Heroicon::OutlinedLink)
                            ->columnSpanFull(),

                        TextInput::make('sort_order')
                            ->label('Sıralama')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Küçük değer önce'),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Pasif bağlantı public sayfada gizlenir.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('label')
                    ->label('Ad')
                    ->weight('semibold')
                    ->searchable(),

                TextColumn::make('platform')
                    ->label('Platform')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => SocialLink::PLATFORMS[$state] ?? $state),

                TextColumn::make('url')
                    ->label('URL')
                    ->limit(50)
                    ->tooltip(fn (SocialLink $record) => $record->url)
                    ->url(fn (SocialLink $record) => $record->url)
                    ->openUrlInNewTab(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->numeric()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
            ->emptyStateHeading('Henüz sosyal bağlantı yok')
            ->emptyStateDescription('İlk bağlantıyı eklemek için üstteki "Yeni Bağlantı" butonuna basın.')
            ->emptyStateIcon(Heroicon::OutlinedGlobeAlt);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSocialLinks::route('/'),
        ];
    }
}
