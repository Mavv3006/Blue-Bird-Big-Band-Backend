<?php

namespace Database\Factories;

use App\Models\Band;
use App\Models\Concert;
use App\Models\Place;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Concert>
 */
class ConcertFactory extends Factory
{
    protected $model = Concert::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $randomDuration = random_int(1, 3);
        $start_time = Carbon::createFromTimestamp($this->faker->dateTimeBetween(
            '-3 years',
            '+3 years'
        )->getTimestamp());
        $end_time = Carbon::createFromFormat('Y-m-d H:i:s', $start_time)->addHours($randomDuration);
        $date = $this->faker->dateTimeThisYear('+12 months');

        return [
            'date' => $date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'place_street' => $this->faker->streetName,
            'place_number' => $this->faker->buildingNumber,
            'place_description' => $this->faker->text(50),
            'organizer_description' => $this->faker->text(50),
            'band_id' => Band::factory()->create(),
            'place_plz' => Place::factory()->create()
        ];
    }
}
