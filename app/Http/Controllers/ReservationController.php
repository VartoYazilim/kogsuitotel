<?php

namespace App\Http\Controllers;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\ReservationCreated;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function create(Request $request): View
    {
        $selectedRoom = null;

        if ($slug = $request->string('oda')->toString()) {
            $selectedRoom = Room::active()->where('slug', $slug)->first();
        }

        return view('reservations.create', [
            'rooms' => Room::active()->ordered()->get(),
            'selectedRoom' => $selectedRoom,
            'prefillCheckIn' => $request->string('check_in')->toString() ?: null,
            'prefillCheckOut' => $request->string('check_out')->toString() ?: null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Honeypot — botlar "website" alanını doldurursa sessizce reddet
        if ($request->filled('website')) {
            return redirect()->route('home');
        }

        $data = $request->validate([
            'guest_first_name' => ['required', 'string', 'max:100'],
            'guest_last_name' => ['required', 'string', 'max:100'],
            'guest_phone' => ['required', 'string', 'max:30'],
            'guest_email' => ['required', 'email', 'max:150'],
            'room_id' => ['required', Rule::exists('rooms', 'id')->where('is_active', true)],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'adults' => ['required', 'integer', 'min:1', 'max:10'],
            'children' => ['nullable', 'integer', 'min:0', 'max:10'],
            'special_requests' => ['nullable', 'string', 'max:1000'],
        ]);

        $room = Room::findOrFail($data['room_id']);
        $checkIn = Carbon::parse($data['check_in']);
        $checkOut = Carbon::parse($data['check_out']);
        $nights = (int) $checkIn->diffInDays($checkOut);

        // Tarih çakışma kontrolü — aktif rezervasyon varsa kabul etme.
        // (Pending rez. çakışma sayılmaz, sadece confirmed/paid/completed.)
        $hasOverlap = Reservation::overlapping($room->id, $checkIn, $checkOut)->exists();
        if ($hasOverlap) {
            return back()
                ->withInput()
                ->withErrors([
                    'check_in' => 'Seçtiğiniz tarihlerde bu oda dolu. Lütfen farklı tarih veya oda seçin.',
                ]);
        }

        $reservation = Reservation::create([
            'room_id' => $room->id,
            'guest_first_name' => $data['guest_first_name'],
            'guest_last_name' => $data['guest_last_name'],
            'guest_phone' => $data['guest_phone'],
            'guest_email' => $data['guest_email'],
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'adults' => $data['adults'],
            'children' => $data['children'] ?? 0,
            'nights' => $nights,
            'total_price' => $room->base_price * $nights,
            'special_requests' => $data['special_requests'] ?? null,
            'status' => ReservationStatus::Pending,
        ]);

        // Admin'lere Filament database notification — header bell ikonu
        $admins = User::where('is_admin', true)->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new ReservationCreated($reservation));
        }

        return redirect()->route('reservations.success', ['code' => $reservation->reservation_code]);
    }

    public function success(string $code): View
    {
        $reservation = Reservation::where('reservation_code', $code)
            ->with('room')
            ->firstOrFail();

        return view('reservations.success', [
            'reservation' => $reservation,
            'iban' => Setting::get('iban'),
            'ibanHolder' => Setting::get('iban_holder'),
            'bankName' => Setting::get('bank_name'),
            'whatsapp' => Setting::get('whatsapp'),
        ]);
    }
}
