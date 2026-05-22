<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'Test Standart', 'Test Suit', 'Test Aile', 'Test Deluxe', 'Test Premium',
        ]).' '.$this->faker->numberBetween(1, 9999);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.$this->faker->unique()->randomNumber(4),
            'description' => $this->faker->paragraph(),
            'capacity' => 6, // sabit yüksek — factory'lerde guest sayısı çakışmasın
            'base_price' => $this->faker->numberBetween(1000, 5000),
            'features' => ['Wi-Fi', 'Klima', 'Smart TV'],
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(0, 100),
        ];
    }
}
