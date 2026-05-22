<?php

namespace App\Http\Controllers;

use App\Enums\ReservationStatus;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(): View
    {
        return view('rooms.index', [
            'rooms' => Room::active()->ordered()->get(),
        ]);
    }

    public function show(Room $room): View
    {
        abort_unless($room->is_active, 404);

        return view('rooms.show', [
            'room' => $room,
            'otherRooms' => Room::active()
                ->ordered()
                ->where('id', '!=', $room->id)
                ->limit(3)
                ->get(),
        ]);
    }

    /**
     * Bir odanın aktif rezervasyon nedeniyle müsait olmayan tarih aralıkları.
     * Flatpickr `disable` option için JSON dizisi döner.
     *
     * Önemli: API tarih aralığı [from, to] (sonu DAHİL, kullanıcıya gösterilen "dolu" günler).
     * DB'deki check_out günü ise oda yeniden müsait olduğu için "to" = check_out - 1.
     *
     * Geçmiş tarihler dönmez (UI'da disabled bunlar zaten min=today ile).
     */
    public function unavailableDates(Room $room): JsonResponse
    {
        $reservations = $room->reservations()
            ->whereIn('status', [
                ReservationStatus::Confirmed,
                ReservationStatus::Paid,
                ReservationStatus::Completed,
            ])
            ->whereDate('check_out', '>=', today())
            ->get(['check_in', 'check_out']);

        $ranges = $reservations->map(fn ($r) => [
            'from' => $r->check_in->format('Y-m-d'),
            'to' => $r->check_out->copy()->subDay()->format('Y-m-d'),
        ])->values();

        return response()->json($ranges)
            ->header('Cache-Control', 'public, max-age=60'); // 1 dk cache
    }
}
