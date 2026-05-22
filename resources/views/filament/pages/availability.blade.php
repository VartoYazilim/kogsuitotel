<x-filament-panels::page>

    {{-- Tarih aralığı seçici --}}
    <x-filament::section>
        <x-slot name="heading">Tarih Aralığı</x-slot>
        <x-slot name="description">Sorgulamak istediğiniz tarih aralığını seçin. Sonuçlar anında güncellenir.</x-slot>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="checkIn" class="block text-sm font-medium mb-1">Giriş Tarihi</label>
                <input type="date" id="checkIn" wire:model.live="checkIn"
                       min="{{ now()->subYear()->format('Y-m-d') }}"
                       class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-white/5 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
            </div>
            <div>
                <label for="checkOut" class="block text-sm font-medium mb-1">Çıkış Tarihi</label>
                <input type="date" id="checkOut" wire:model.live="checkOut"
                       min="{{ now()->format('Y-m-d') }}"
                       class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-white/5 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" />
            </div>
            <div class="flex items-end">
                <div class="w-full rounded-lg p-3 text-center"
                     style="background: rgba(107, 117, 83, 0.15); border: 1px solid rgba(107, 117, 83, 0.3);">
                    <p style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.7;">Konaklama</p>
                    <p style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin-top: 0.25rem;">
                        {{ $this->getNightsCount() }} gece
                    </p>
                </div>
            </div>
        </div>
    </x-filament::section>

    {{-- Sonuçlar --}}
    <div class="space-y-4">
        @php $statuses = $this->getRoomsStatus(); @endphp

        @if ($statuses->isEmpty())
            <x-filament::section>
                <div class="text-center py-8 text-gray-500">
                    Geçerli bir tarih aralığı girin.
                </div>
            </x-filament::section>
        @else
            {{-- Özet stat --}}
            @php
                $availableCount = $statuses->where('is_available', true)->count();
                $bookedCount = $statuses->where('is_available', false)->count();
                $pendingCount = $statuses->where('has_pending_warning', true)->count();
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-lg p-4"
                     style="background: rgba(90, 138, 94, 0.12); border: 1px solid rgba(90, 138, 94, 0.3);">
                    <p style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.75;">Müsait</p>
                    <p style="font-weight: 700; font-size: 2rem; line-height: 1.1; margin-top: 0.25rem; color: #5a8a5e;">
                        {{ $availableCount }} <span style="font-size: 0.875rem; opacity: 0.6;">/ {{ $statuses->count() }} oda</span>
                    </p>
                </div>

                <div class="rounded-lg p-4"
                     style="background: rgba(177, 75, 58, 0.10); border: 1px solid rgba(177, 75, 58, 0.3);">
                    <p style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.75;">Dolu</p>
                    <p style="font-weight: 700; font-size: 2rem; line-height: 1.1; margin-top: 0.25rem; color: #b14b3a;">
                        {{ $bookedCount }} <span style="font-size: 0.875rem; opacity: 0.6;">/ {{ $statuses->count() }} oda</span>
                    </p>
                </div>

                <div class="rounded-lg p-4"
                     style="background: rgba(196, 154, 77, 0.12); border: 1px solid rgba(196, 154, 77, 0.3);">
                    <p style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.75;">Onay Bekleyen Çakışma</p>
                    <p style="font-weight: 700; font-size: 2rem; line-height: 1.1; margin-top: 0.25rem; color: #c49a4d;">
                        {{ $pendingCount }} <span style="font-size: 0.875rem; opacity: 0.6;">oda</span>
                    </p>
                </div>
            </div>

            {{-- Oda kart listesi --}}
            @foreach ($statuses as $row)
                <x-filament::section>
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            @if ($row['is_available'])
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold"
                                      style="background: rgba(90, 138, 94, 0.15); color: #5a8a5e;">
                                    <span style="display: inline-block; width: 6px; height: 6px; border-radius: 9999px; background: #5a8a5e;"></span>
                                    Müsait
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold"
                                      style="background: rgba(177, 75, 58, 0.15); color: #b14b3a;">
                                    <span style="display: inline-block; width: 6px; height: 6px; border-radius: 9999px; background: #b14b3a;"></span>
                                    Dolu
                                </span>
                            @endif

                            <div>
                                <h3 class="font-semibold text-base">{{ $row['room']->name }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $row['room']->capacity }} kişilik
                                    · ₺{{ number_format($row['room']->base_price, 0, ',', '.') }}/gece
                                    @if ($row['nights'] > 0)
                                        · <strong style="color: #6b7553;">Toplam ₺{{ number_format($row['total_price'], 0, ',', '.') }}</strong>
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if ($row['is_available'])
                            <a href="{{ \App\Filament\Resources\Reservations\ReservationResource::getUrl('create') }}"
                               class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-semibold"
                               style="background: #6b7553; color: #fff;">
                                Bu Odaya Rezervasyon
                            </a>
                        @endif
                    </div>

                    @if ($row['overlapping']->isNotEmpty())
                        <div class="mt-4 pt-4" style="border-top: 1px solid rgba(0,0,0,0.08);">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">
                                Çakışan Aktif Rezervasyonlar
                            </p>
                            <ul class="space-y-1.5">
                                @foreach ($row['overlapping'] as $rez)
                                    <li class="flex flex-wrap items-center gap-2 text-sm">
                                        <a href="{{ \App\Filament\Resources\Reservations\ReservationResource::getUrl('view', ['record' => $rez]) }}"
                                           class="font-mono font-semibold" style="color: #6b7553;">
                                            {{ $rez->reservation_code }}
                                        </a>
                                        <span class="text-gray-500">·</span>
                                        <span>{{ $rez->guest_first_name }} {{ $rez->guest_last_name }}</span>
                                        <span class="text-gray-500">·</span>
                                        <span>{{ $rez->check_in->format('d.m.Y') }} → {{ $rez->check_out->format('d.m.Y') }}</span>
                                        <span class="text-xs rounded-full px-2 py-0.5"
                                              style="background: rgba(107, 117, 83, 0.15); color: #4a5240;">
                                            {{ $rez->status->getLabel() }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($row['pending']->isNotEmpty())
                        <div class="mt-3 rounded-lg p-3"
                             style="background: rgba(196, 154, 77, 0.10); border: 1px solid rgba(196, 154, 77, 0.3);">
                            <p class="text-xs">
                                <strong style="color: #c49a4d;">Dikkat:</strong>
                                Bu odada onay bekleyen {{ $row['pending']->count() }} rezervasyon talebi var
                                (çakışan tarih aralığında).
                            </p>
                        </div>
                    @endif
                </x-filament::section>
            @endforeach
        @endif
    </div>

</x-filament-panels::page>
