<?php

namespace Database\Seeders;

use App\Models\ConcertRecording;
use Illuminate\Database\Seeder;

class ConcertRecordingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ConcertRecording::factory()->count(25)->create();
    }
}
