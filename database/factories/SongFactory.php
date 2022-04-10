<?php

namespace Database\Factories;

use App\Models\Song;
use App\Models\SongType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Song>
 */
class SongFactory extends Factory
{
    protected $model = Song::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'file_name' => 'fever.mp3',
            'song_name' => 'Fever',
            'genre' => 'Swing',
            'arranger' => 'Roger Holmes',
            'author' => 'J. Davenport, E. Cooley',
            'type' => SongType::all()->random()->id,
            'size' => $this->faker->randomNumber(2)
        ];
    }
}
