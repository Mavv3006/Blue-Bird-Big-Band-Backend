<?php

namespace Database\Factories;

use App\Models\SongType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SongType>
 */
class SongTypeFactory extends Factory
{
    protected $model = SongType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'type' => $this->faker->colorName,
            'folder_name' => 'songs'
        ];
    }
}
