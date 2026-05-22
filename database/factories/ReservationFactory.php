<?php

namespace Database\Factories;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    public function definition(): array
    {
        $checkIn = $this->faker->dateTimeBetween('+1 day', '+60 days');
        $nights = $this->faker->numberBetween(1, 5);
        $checkOut = (clone $checkIn)->modify("+{$nights} days");

        return [
            'room_id' => Room::factory(),
            'guest_first_name' => $this->faker->firstName(),
            'guest_last_name' => $this->faker->lastName(),
            'guest_phone' => '+90 555 '.$this->faker->numerify('### ## ##'),
            'guest_email' => $this->faker->safeEmail(),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            // Default değerler — guard'lar (kapasite) çakışmasın
            'adults' => 1,
            'children' => 0,
            'total_price' => $this->faker->numberBetween(1000, 10000),
            'status' => ReservationStatus::Pending,
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => ReservationStatus::Pending]);
    }

    public function paid(): static
    {
        return $this->state(['status' => ReservationStatus::Paid]);
    }

    public function completed(): static
    {
        return $this->state(['status' => ReservationStatus::Completed]);
    }
}
