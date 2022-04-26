<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Concert;
use App\Models\ConcertRecording;
use App\Models\Song;
use App\Models\SongType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ConcertsRecordingsControllerTest extends TestCase
{
    use RefreshDatabase;

    private string $recording_path = 'api/download/recording';
    private string $recordings_path = 'api/download/recordings';

    public function test_download_the_right_file()
    {
        Concert::factory()->create();
        SongType::factory()->create();

        $storage = Storage::fake('local');
        $file_name = "test_get_one_file.mp3";
        $file = File::create($file_name, 100);
        $file_path = $file->storeAs('recordings', $file_name, 'local');
        ConcertRecording::factory()->create(['file_name' => $file_name]);


        $response = $this->get($this->recording_path . '?file_name=' . $file_name, $this->auth_header());
        $response->assertStatus(200);

        Storage::shouldReceive('disk')
            ->with('local')
            ->andReturn($storage)
            ->shouldReceive('download')
            ->with($file_path)
            ->andReturn($file);
    }

    public function test_fail_download()
    {
        Storage::fake('local');
        $file_name = 'test.mp3';
        File::create($file_name, 100)->storeAs('recordings', $file_name, 'local');
        SongType::factory()->create();
        Song::factory()->create(['file_name' => $file_name,]);

        $this
            ->get($this->recording_path . '?file_name=bla_bla', $this->auth_header())
            ->assertStatus(404)
            ->assertJsonStructure(['error'])
            ->assertHeader('content-type', 'application/json');
    }

    public function test_auth()
    {
//        $this->withoutExceptionHandling();

        $this
            ->get($this->recording_path . '?file_name=test.mp3', $this->accept_header())
            ->assertStatus(401)
            ->assertHeader('content-type', 'application/json')
            ->assertJsonStructure(['message']);
    }

    public function test_no_query_parameter()
    {
        Storage::fake('local');
        $file_name = 'test.mp3';
        File::create($file_name, 100)->storeAs('recordings', $file_name, 'local');
        SongType::factory()->create();
        Song::factory()->create(['file_name' => $file_name,]);

        $this
            ->get($this->recording_path, $this->auth_header())
            ->assertStatus(400)
            ->assertJsonStructure(['error', 'message']);
    }

    public function test_download_gitkeep()
    {
        Storage::fake('local');
        $file_name = '.gitkeep';
        File::create($file_name, 100)->storeAs('recordings', $file_name, 'local');

        $this
            ->get($this->recording_path . '?file_name=' . $file_name, $this->auth_header())
            ->assertStatus(404);
    }

    public function test_concert_recordings_correct()
    {
        $json_structure = [
            '*' => [
                'concert' => [
                    'date',
                    'description',
                    'place'
                ],
                'files' => [
                    '*' => [
                        'description',
                        'file_size',
                        'file_name'
                    ]
                ]
            ]
        ];

        Concert::factory()->create();
        $concert = Concert::first();
        SongType::factory()->create();
        $concertRecording = ConcertRecording::factory()->create();

        $json_exact = [
            [
                'concert' => [
                    'description' => $concert->place_description,
                    'date' => $concert->date(),
                    'place' => $concert->place->name
                ],
                'files' => [
                    [
                        'description' => $concertRecording->description,
                        'file_size' => $concertRecording->size,
                        'file_name' => $concertRecording->file_name
                    ]
                ]
            ]
        ];

        $this
            ->get($this->recordings_path, $this->auth_header())
            ->assertStatus(200)
            ->assertHeader('content-type', 'application/json')
            ->assertJsonStructure($json_structure)
            ->assertJson($json_exact);
    }

    public function test_concert_recordings_force_use_auth()
    {
        $this
            ->get($this->recordings_path, $this->accept_header())
            ->assertStatus(401)
            ->assertHeader('content-type', 'application/json')
            ->assertJsonStructure(['message']);
    }
}
