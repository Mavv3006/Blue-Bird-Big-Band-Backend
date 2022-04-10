<?php

namespace Database\Factories;

use App\Models\Concert;
use App\Models\ConcertRecording;
use App\Models\SongType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConcertRecording>
 */
class ConcertRecordingFactory extends Factory
{
    protected $model = ConcertRecording::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'file_name' => $this->faker->slug(2, false),
            'description' => $this->faker->name,
            'size' => mt_rand(5 * 10, 25 * 10) / 10,
            'concerts_id' => Concert::all()->random()->id,
            'type' => SongType::all()->random()->id
        ];
    }
}
