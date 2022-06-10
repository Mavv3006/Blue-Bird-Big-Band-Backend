<?php

namespace Database\Seeders;

use App\Models\Band;
use Illuminate\Database\Seeder;

class ProdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Band::factory()->create(["name" => "Blue Bird Big Band"]);
        Band::factory()->create(["name" => "Dome Town Band"]);

        $this->call(SongTypeSeeder::class);
    }
}
