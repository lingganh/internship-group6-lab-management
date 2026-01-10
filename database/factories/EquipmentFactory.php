<?php

namespace Database\Factories;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Factories\Factory;

class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    public function definition(): array
    {
        $purchased = $this->faker->optional(0.8)->dateTimeBetween('-5 years', 'now');

        $spec = $this->faker->optional(0.7)->randomElement([
            ['cpu' => 'i5', 'ram' => '16GB', 'ssd' => '512GB'],
            ['ports' => 24, 'speed' => '1Gbps'],
            ['lumens' => 3500, 'resolution' => '1080p'],
        ]);

        return [
            'name'           => $this->faker->randomElement(['Máy tính', 'Máy chiếu', 'Router', 'Switch', 'Oscilloscope', 'Multimeter'])
                . ' ' . $this->faker->bothify('##?'),
            'code'           => strtoupper($this->faker->unique()->bothify('EQ-###-??')),
            'type'           => $this->faker->randomElement(['IT', 'Electronics', 'Network']),
            'status'         => $this->faker->randomElement(['available', 'in_use', 'maintenance', 'broken']),
            'purchased_date' => $purchased?->format('Y-m-d'),
            'specifications' => $spec ? json_encode($spec, JSON_UNESCAPED_UNICODE) : null,
            'notes'          => $this->faker->optional(0.4)->sentence(),
        ];
    }
}
