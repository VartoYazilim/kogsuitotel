<?php

namespace App\Filament\Resources\Rooms\Tables;

use App\Models\Room;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class RoomsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('Kapak')
                    ->getStateUsing(fn (Room $record): ?string => $record->cover_image_url)
                    ->square()
                    ->width(64)
                    ->height(64),

                TextColumn::make('name')
                    ->label('Oda Adı')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('capacity')
                    ->label('Kapasite')
                    ->numeric()
                    ->suffix(' kişi')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('base_price')
                    ->label('Gecelik')
                    ->money('TRY')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

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
                TernaryFilter::make('is_active')
                    ->label('Durum')
                    ->placeholder('Hepsi')
                    ->trueLabel('Sadece Aktif')
                    ->falseLabel('Sadece Pasif'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
