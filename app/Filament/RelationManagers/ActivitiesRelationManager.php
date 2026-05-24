<?php

namespace App\Filament\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

/**
 * Spatie ActivityLog için ortak Filament Relation Manager.
 * Reservation, Room, Setting (vb.) detay sayfalarında "Geçmiş" sekmesi olarak görünür.
 *
 * Hem KVKK m.12/3 denetim (kim ne zaman ne değiştirdi) hem operasyonel
 * şeffaflık için: sahip "IBAN ne zaman değişti?" sorusunda raw SQL'e bakmaz.
 */
class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    protected static ?string $title = 'Geçmiş';

    // Lazy load — sayfa açılırken tablo render edilmez, "Yükle" butonu görünür.
    // Tıklayınca tablo açılır (gizle/göster davranışı). Plus performans:
    // her detay sayfasında activity_log sorgusu otomatik koşmaz.
    protected static bool $isLazy = true;

    public function form(Schema $schema): Schema
    {
        // Activity log salt okunur — form yok
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50])
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable()
                    ->color('gray'),

                TextColumn::make('causer.name')
                    ->label('Kullanıcı')
                    ->default('Sistem')
                    ->color('gray'),

                TextColumn::make('event')
                    ->label('Olay')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'created' => 'Oluşturuldu',
                        'updated' => 'Güncellendi',
                        'deleted' => 'Silindi',
                        default => (string) $state,
                    }),

                TextColumn::make('changes')
                    ->label('Değişiklikler')
                    ->state(fn (Activity $record): string => self::formatChanges($record))
                    ->html()
                    ->wrap(),
            ])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }

    /**
     * Spatie ActivityLog properties.old/attributes diff'i HTML olarak render eder.
     * 'created' → sadece yeni değerler; 'updated' → eski → yeni okuyabilir format.
     */
    public static function formatChanges(Activity $record): string
    {
        /** @var array<string, mixed> $old */
        $old = $record->properties->get('old') ?? [];
        /** @var array<string, mixed> $attr */
        $attr = $record->properties->get('attributes') ?? [];

        if (empty($attr) && empty($old)) {
            return '<span class="text-gray-400 italic">— veri yok —</span>';
        }

        $rows = [];
        foreach ($attr as $key => $newValue) {
            $oldValue = $old[$key] ?? null;
            $newStr = is_scalar($newValue) ? (string) $newValue : json_encode($newValue, JSON_UNESCAPED_UNICODE);

            if ($oldValue === null || $oldValue === '') {
                $rows[] = sprintf(
                    '<div><span class="font-mono text-xs text-gray-500">%s:</span> <span class="font-medium">%s</span></div>',
                    e($key),
                    e($newStr)
                );
            } else {
                $oldStr = is_scalar($oldValue) ? (string) $oldValue : json_encode($oldValue, JSON_UNESCAPED_UNICODE);
                $rows[] = sprintf(
                    '<div><span class="font-mono text-xs text-gray-500">%s:</span> <s class="text-gray-400">%s</s> → <span class="font-medium">%s</span></div>',
                    e($key),
                    e($oldStr),
                    e($newStr)
                );
            }
        }

        return implode('', $rows);
    }
}
