<x-filament-panels::page>

    {{--
        NOT: Filament panel Tailwind v4 build'i `resources/views/filament/pages/*.blade.php`
        dosyalarini SCAN ETMEZ → grid/flex/font/padding gibi utility class'lari calismaz.
        Bu Blade'de Filament built-in component'lar (<x-filament::section>) + inline style
        ile layout/typography yapilir. Filament custom panel theme kurulursa (Faz 3 oncesi)
        Tailwind class'a donulebilir. Detay: feedback-test-coverage-discipline memory.
    --}}

    {{-- Tarih araligi secici --}}
    <x-filament::section>
        <x-slot name="heading">Tarih Aralığı</x-slot>
        <x-slot name="description">Sorgulamak istediğiniz tarih aralığını seçin. Sonuçlar anında güncellenir.</x-slot>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; align-items: end;">
            <div>
                <label for="checkIn" style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.25rem;">Giriş Tarihi</label>
                <input type="date" id="checkIn" wire:model.live="checkIn"
                       min="{{ now()->subYear()->format('Y-m-d') }}"
                       class="fi-input"
                       style="display:block; width:100%; padding:0.5rem 0.75rem; border:1px solid #d9d2c2; border-radius:0.5rem; font-size:0.875rem; background:#fff;" />
            </div>
            <div>
                <label for="checkOut" style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.25rem;">Çıkış Tarihi</label>
                <input type="date" id="checkOut" wire:model.live="checkOut"
                       min="{{ now()->format('Y-m-d') }}"
                       class="fi-input"
                       style="display:block; width:100%; padding:0.5rem 0.75rem; border:1px solid #d9d2c2; border-radius:0.5rem; font-size:0.875rem; background:#fff;" />
            </div>
            <div style="background: rgba(107, 117, 83, 0.10); border: 1px solid rgba(107, 117, 83, 0.25); border-radius: 0.5rem; padding: 0.75rem; text-align: center;">
                <p style="font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.7; margin:0;">Konaklama</p>
                <p style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0 0 0; color: #4a5240;">
                    {{ $this->getNightsCount() }} gece
                </p>
            </div>
        </div>
    </x-filament::section>

    {{-- Sonuclar --}}
    <div style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem;">
        @php $statuses = $this->getRoomsStatus(); @endphp

        @if ($statuses->isEmpty())
            <x-filament::section>
                <div style="text-align:center; padding:2rem 0; color:#6b6e62;">
                    Geçerli bir tarih aralığı girin.
                </div>
            </x-filament::section>
        @else
            @php
                $availableCount = $statuses->where('is_available', true)->count();
                $bookedCount = $statuses->where('is_available', false)->count();
                $pendingCount = $statuses->where('has_pending_warning', true)->count();
            @endphp

            {{-- Ozet stat 3 kart --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div style="border-radius: 0.5rem; padding: 1rem; background: rgba(90, 138, 94, 0.10); border: 1px solid rgba(90, 138, 94, 0.30);">
                    <p style="font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.75; margin:0;">Müsait</p>
                    <p style="font-weight: 700; font-size: 2rem; line-height: 1.1; margin: 0.25rem 0 0 0; color: #4a7c4e;">
                        {{ $availableCount }} <span style="font-size: 0.875rem; opacity: 0.6;">/ {{ $statuses->count() }} oda</span>
                    </p>
                </div>

                <div style="border-radius: 0.5rem; padding: 1rem; background: rgba(177, 75, 58, 0.08); border: 1px solid rgba(177, 75, 58, 0.30);">
                    <p style="font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.75; margin:0;">Dolu</p>
                    <p style="font-weight: 700; font-size: 2rem; line-height: 1.1; margin: 0.25rem 0 0 0; color: #8d3b2e;">
                        {{ $bookedCount }} <span style="font-size: 0.875rem; opacity: 0.6;">/ {{ $statuses->count() }} oda</span>
                    </p>
                </div>

                <div style="border-radius: 0.5rem; padding: 1rem; background: rgba(196, 154, 77, 0.10); border: 1px solid rgba(196, 154, 77, 0.30);">
                    <p style="font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.75; margin:0;">Onay Bekleyen Çakışma</p>
                    <p style="font-weight: 700; font-size: 2rem; line-height: 1.1; margin: 0.25rem 0 0 0; color: #8b6f3a;">
                        {{ $pendingCount }} <span style="font-size: 0.875rem; opacity: 0.6;">oda</span>
                    </p>
                </div>
            </div>

            {{-- Oda kartlari --}}
            @foreach ($statuses as $row)
                <x-filament::section>
                    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                            @if ($row['is_available'])
                                <span style="display:inline-flex; align-items:center; gap:0.375rem; border-radius:9999px; padding:0.25rem 0.75rem; font-size:0.75rem; font-weight:600; background: rgba(90, 138, 94, 0.15); color: #4a7c4e;">
                                    <span style="display:inline-block; width:6px; height:6px; border-radius:9999px; background:#5a8a5e;"></span>
                                    Müsait
                                </span>
                            @else
                                <span style="display:inline-flex; align-items:center; gap:0.375rem; border-radius:9999px; padding:0.25rem 0.75rem; font-size:0.75rem; font-weight:600; background: rgba(177, 75, 58, 0.15); color: #8d3b2e;">
                                    <span style="display:inline-block; width:6px; height:6px; border-radius:9999px; background:#b14b3a;"></span>
                                    Dolu
                                </span>
                            @endif

                            <div>
                                <h3 style="font-weight:600; font-size:1rem; margin:0; color:#2a2d24;">{{ $row['room']->name }}</h3>
                                <p style="font-size:0.75rem; color:#6b6e62; margin: 0.125rem 0 0 0;">
                                    {{ $row['room']->capacity }} kişilik
                                    · ₺{{ number_format($row['room']->base_price, 0, ',', '.') }}/gece
                                    @if ($row['nights'] > 0)
                                        · <strong style="color: #4a5240;">Toplam ₺{{ number_format($row['total_price'], 0, ',', '.') }}</strong>
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if ($row['is_available'])
                            <a href="{{ \App\Filament\Resources\Reservations\ReservationResource::getUrl('create') }}"
                               style="display:inline-flex; align-items:center; gap:0.25rem; border-radius:0.5rem; padding:0.5rem 0.875rem; font-size:0.75rem; font-weight:600; background: #6b7553; color: #fff; text-decoration:none;">
                                Bu Odaya Rezervasyon
                            </a>
                        @endif
                    </div>

                    @if ($row['overlapping']->isNotEmpty())
                        <div style="margin-top:1rem; padding-top:1rem; border-top: 1px solid rgba(0,0,0,0.08);">
                            <p style="font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; color:#6b6e62; margin: 0 0 0.5rem 0;">
                                Çakışan Aktif Rezervasyonlar
                            </p>
                            <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:0.375rem;">
                                @foreach ($row['overlapping'] as $rez)
                                    <li style="display:flex; flex-wrap:wrap; align-items:center; gap:0.5rem; font-size:0.875rem;">
                                        <a href="{{ \App\Filament\Resources\Reservations\ReservationResource::getUrl('view', ['record' => $rez]) }}"
                                           style="font-family:monospace; font-weight:600; color:#4a5240; text-decoration:none;">
                                            {{ $rez->reservation_code }}
                                        </a>
                                        <span style="color:#9da099;">·</span>
                                        <span style="color:#2a2d24;">{{ $rez->guest_first_name }} {{ $rez->guest_last_name }}</span>
                                        <span style="color:#9da099;">·</span>
                                        <span style="color:#2a2d24;">{{ $rez->check_in->format('d.m.Y') }} → {{ $rez->check_out->format('d.m.Y') }}</span>
                                        <span style="font-size:0.75rem; border-radius:9999px; padding:0.125rem 0.5rem; background: rgba(107, 117, 83, 0.15); color: #4a5240;">
                                            {{ $rez->status->getLabel() }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($row['pending']->isNotEmpty())
                        <div style="margin-top:0.75rem; border-radius:0.5rem; padding:0.75rem; background: rgba(196, 154, 77, 0.10); border: 1px solid rgba(196, 154, 77, 0.30);">
                            <p style="font-size:0.75rem; margin:0; color:#2a2d24;">
                                <strong style="color:#8b6f3a;">Dikkat:</strong>
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
