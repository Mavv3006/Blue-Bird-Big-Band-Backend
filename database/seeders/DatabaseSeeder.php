<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this
            ->call(PlaceSeeder::class)
            ->call(BandSeeder::class)
            ->call(ConcertSeeder::class)
            ->call(UserSeeder::class)
            ->call(SongTypeSeeder::class)
            ->call(SongSeeder::class)
            ->call(ConcertRecordingSeeder::class);
    }
}
