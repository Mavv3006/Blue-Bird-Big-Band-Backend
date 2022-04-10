<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Song;
use App\Models\SongType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SongsControllerTest extends TestCase
{
    use RefreshDatabase;

    private string $song_route = 'api/download/song';
    private string $songs_route = 'api/download/songs';

    public function test_download_the_right_file()
    {
        $storage = Storage::fake('local');
        $file_name = "test_get_one_file.mp3";
        $file = File::create($file_name, 100);
        $file_path = $file->storeAs('songs', $file_name, 'local');
        SongType::factory()->create();
        Song::factory()->create(['file_name' => $file_name]);

        $this
            ->get($this->song_route . '?file_name=' . $file_name, /*$this->getLoginHeader()*/)
            ->assertStatus(200);

        Storage::shouldReceive('disk')
            ->with('local')
            ->andReturn($storage)
            ->shouldReceive('download')
            ->with($file_path)
            ->andReturn($file);
    }

    public function test_file_name_in_request_body()
    {
        Storage::fake('local');
        $file_name = "test_get_one_file.mp3";
        $file = File::create($file_name, 100);
        $file->storeAs('songs', $file_name, 'local');
        SongType::factory()->create();
        Song::factory()->create(['file_name' => $file_name]);

        $response = $this->json('GET', $this->song_route, ['file_name' => $file_name], /*$this->getLoginHeader()*/);

        $response
            ->assertStatus(400)
            ->assertHeader('content-type', 'application/json')
            ->assertJsonStructure(['error', 'message']);
    }

    public function test_fail_download()
    {
        $file_name = 'test.mp3';
        File::create($file_name, 100)->storeAs('songs', $file_name, 'local');
        SongType::factory()->create();
        Song::factory()->create(['file_name' => $file_name]);

        $this->get($this->song_route . '?file_name=bla_bla', /*$this->getLoginHeader()*/)
            ->assertStatus(404)
            ->assertHeader('content-type', 'application/json')
            ->assertJsonStructure(['error']);
    }

    public function test_auth()
    {
        $this
            ->get($this->song_route . '?file_name=test.mp3')
            ->assertStatus(401)
            ->assertHeader('content-type', 'application/json')
            ->assertJsonStructure(['error']);
    }

    public function test_no_query_parameter()
    {
        $file_name = 'test.mp3';
        File::create($file_name, 100)->storeAs('songs', $file_name, 'local');
        SongType::factory()->create();
        Song::factory()->create(['file_name' => $file_name]);

        $this
            ->get($this->song_route, /*$this->getLoginHeader()*/)
            ->assertStatus(400)
            ->assertJsonStructure(['error', 'message']);
    }

    public function test_download_gitkeep()
    {
        Storage::fake('local');
        $file_name = '.gitkeep';
        File::create($file_name, 100)->storeAs('songs', $file_name, 'local');

        $this
            ->get($this->song_route . '?file_name=' . $file_name, /*$this->getLoginHeader()*/)
            ->assertStatus(404);
    }

    public function test_get_all_songs_correct()
    {
        $json_structure = [
            [
                'arranger',
                'author',
                'file_name',
                'genre',
                'title'
            ]
        ];

        SongType::factory()->create();
        Song::factory()->count(3)->create();

        $this
            ->get($this->songs_route, /*$this->getLoginHeader()*/)
            ->assertStatus(200)
            ->assertHeader('content-type', 'application/json')
            ->assertJsonStructure($json_structure);
    }

    public function test_all_songs_force_use_auth()
    {
        $this
            ->get($this->songs_route)
            ->assertStatus(401)
            ->assertHeader('content-type', 'application/json')
            ->assertJsonStructure(['error']);
    }

    public function test_all_songs_no_songs()
    {
        $this
            ->get($this->songs_route, /*$this->getLoginHeader()*/)
            ->assertStatus(200)
            ->assertHeader('content-type', 'application/json')
            ->assertJsonStructure(['*' => []]);
    }
}
