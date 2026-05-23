<x-filament-panels::page>

    {{-- Tarih aralığı seçici --}}
    <x-filament::section>
        <x-slot name="heading">Tarih Aralığı</x-slot>
        <x-slot name="description">Sorgulamak istediğiniz tarih aralığını seçin. Sonuçlar anında güncellenir.</x-slot>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label for="checkIn" class="block text-sm font-medium text-gray-950 dark:text-white mb-1">Giriş Tarihi</label>
                <input type="date" id="checkIn" wire:model.live="checkIn"
                       min="{{ now()->subYear()->format('Y-m-d') }}"
                       class="fi-input block w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-sm text-gray-950 dark:text-white shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 outline-none" />
            </div>
            <div>
                <label for="checkOut" class="block text-sm font-medium text-gray-950 dark:text-white mb-1">Çıkış Tarihi</label>
                <input type="date" id="checkOut" wire:model.live="checkOut"
                       min="{{ now()->format('Y-m-d') }}"
                       class="fi-input block w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-sm text-gray-950 dark:text-white shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 outline-none" />
            </div>
            <div class="rounded-lg p-3 text-center bg-primary-50 border border-primary-200 dark:bg-primary-500/10 dark:border-primary-500/30">
                <p class="text-[11px] uppercase tracking-wider text-gray-600 dark:text-gray-400">Konaklama</p>
                <p class="mt-1 text-2xl font-bold leading-tight text-primary-700 dark:text-primary-300">
                    {{ $this->getNightsCount() }} gece
                </p>
            </div>
        </div>
    </x-filament::section>

    {{-- Sonuçlar --}}
    <div class="space-y-4">
        @php $statuses = $this->getRoomsStatus(); @endphp

        @if ($statuses->isEmpty())
            <x-filament::section>
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    Geçerli bir tarih aralığı girin.
                </div>
            </x-filament::section>
        @else
            @php
                $availableCount = $statuses->where('is_available', true)->count();
                $bookedCount = $statuses->where('is_available', false)->count();
                $pendingCount = $statuses->where('has_pending_warning', true)->count();
            @endphp

            {{-- Özet stat 3 kart --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-lg p-4 bg-success-50 border border-success-200 dark:bg-success-500/10 dark:border-success-500/30">
                    <p class="text-[11px] uppercase tracking-wider text-gray-600 dark:text-gray-400">Müsait</p>
                    <p class="mt-1 text-3xl font-bold leading-tight text-success-700 dark:text-success-300">
                        {{ $availableCount }}
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">/ {{ $statuses->count() }} oda</span>
                    </p>
                </div>

                <div class="rounded-lg p-4 bg-danger-50 border border-danger-200 dark:bg-danger-500/10 dark:border-danger-500/30">
                    <p class="text-[11px] uppercase tracking-wider text-gray-600 dark:text-gray-400">Dolu</p>
                    <p class="mt-1 text-3xl font-bold leading-tight text-danger-700 dark:text-danger-300">
                        {{ $bookedCount }}
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">/ {{ $statuses->count() }} oda</span>
                    </p>
                </div>

                <div class="rounded-lg p-4 bg-warning-50 border border-warning-200 dark:bg-warning-500/10 dark:border-warning-500/30">
                    <p class="text-[11px] uppercase tracking-wider text-gray-600 dark:text-gray-400">Onay Bekleyen Çakışma</p>
                    <p class="mt-1 text-3xl font-bold leading-tight text-warning-700 dark:text-warning-300">
                        {{ $pendingCount }}
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">oda</span>
                    </p>
                </div>
            </div>

            {{-- Oda kartları --}}
            @foreach ($statuses as $row)
                <x-filament::section>
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex items-center gap-3 flex-wrap">
                            @if ($row['is_available'])
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold bg-success-100 text-success-700 dark:bg-success-500/20 dark:text-success-300">
                                    <span class="size-1.5 rounded-full bg-success-500"></span>
                                    Müsait
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold bg-danger-100 text-danger-700 dark:bg-danger-500/20 dark:text-danger-300">
                                    <span class="size-1.5 rounded-full bg-danger-500"></span>
                                    Dolu
                                </span>
                            @endif

                            <div>
                                <h3 class="font-semibold text-base text-gray-950 dark:text-white">{{ $row['room']->name }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ $row['room']->capacity }} kişilik
                                    · ₺{{ number_format($row['room']->base_price, 0, ',', '.') }}/gece
                                    @if ($row['nights'] > 0)
                                        · <strong class="text-primary-700 dark:text-primary-300">Toplam ₺{{ number_format($row['total_price'], 0, ',', '.') }}</strong>
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if ($row['is_available'])
                            <a href="{{ \App\Filament\Resources\Reservations\ReservationResource::getUrl('create') }}"
                               class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-semibold bg-primary-600 hover:bg-primary-700 text-white transition-colors">
                                Bu Odaya Rezervasyon
                            </a>
                        @endif
                    </div>

                    @if ($row['overlapping']->isNotEmpty())
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-white/10">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">
                                Çakışan Aktif Rezervasyonlar
                            </p>
                            <ul class="space-y-1.5">
                                @foreach ($row['overlapping'] as $rez)
                                    <li class="flex flex-wrap items-center gap-2 text-sm">
                                        <a href="{{ \App\Filament\Resources\Reservations\ReservationResource::getUrl('view', ['record' => $rez]) }}"
                                           class="font-mono font-semibold text-primary-700 dark:text-primary-300 hover:underline">
                                            {{ $rez->reservation_code }}
                                        </a>
                                        <span class="text-gray-400">·</span>
                                        <span class="text-gray-950 dark:text-white">{{ $rez->guest_first_name }} {{ $rez->guest_last_name }}</span>
                                        <span class="text-gray-400">·</span>
                                        <span class="text-gray-950 dark:text-white">{{ $rez->check_in->format('d.m.Y') }} → {{ $rez->check_out->format('d.m.Y') }}</span>
                                        <span class="text-xs rounded-full px-2 py-0.5 bg-primary-100 text-primary-700 dark:bg-primary-500/20 dark:text-primary-300">
                                            {{ $rez->status->getLabel() }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($row['pending']->isNotEmpty())
                        <div class="mt-3 rounded-lg p-3 bg-warning-50 border border-warning-200 dark:bg-warning-500/10 dark:border-warning-500/30">
                            <p class="text-xs text-gray-950 dark:text-white">
                                <strong class="text-warning-700 dark:text-warning-300">Dikkat:</strong>
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
