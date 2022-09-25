<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Band;
use App\Models\Concert;
use App\Models\Place;
use Carbon\Carbon;
use DateTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConcertsControllerTest extends TestCase
{
    use RefreshDatabase;

    private string $past_route = 'api/concerts/past';
    private string $upcoming_route = 'api/concerts/upcoming';
    private string $all_route = 'api/concerts/all';
    private string $store_route = 'api/concerts';

    private array $concert_data = [
        'date' => '2022-08-12',
        'start_time' => '10:00:00',
        'end_time' => '12:43:12',
        'band_name' => 'test',
        'location' => [
            'street' => 'test',
            'number' => 'test',
            'plz' => 10002,
            'name' => 'test',
        ],
        'description' => [
            'place' => 'test',
            'organizer' => 'test',
        ],
    ];

    public function test_past_concerts_with_concerts_for_in_the_future()
    {
        $this->generate_future_concert();
        $response = $this->get($this->past_route);
        $response->assertJson([], true);
    }

    public function test_concerts_all_returns_all_concerts()
    {
        $this->generate_future_concert();
        $this->generate_past_concert();
        $this->assertCount(2, Concert::all());

        $response = $this->get($this->all_route);

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/json');

        $response_count = $response->baseResponse->content();
        $this->assertCount(2, json_decode($response_count, true));
        $this->assertCount(2, json_decode($response_count, false));
    }

    public function test_response_structure()
    {
        $json_structure = [
            '*' => [
                'date',
                'start_time',
                'end_time',
                'band_name',
                'location' => [
                    'street',
                    'number',
                    'plz',
                    'name'
                ]
            ]
        ];

        Concert::factory()->create();

        $this
            ->get($this->all_route)
            ->assertStatus(200)
            ->assertJsonStructure($json_structure);

        $this
            ->get($this->upcoming_route)
            ->assertStatus(200)
            ->assertJsonStructure($json_structure);

        $this
            ->get($this->past_route)
            ->assertStatus(200)
            ->assertJsonStructure($json_structure);
    }

    public function test_upcoming_including_today()
    {
        Concert::factory()->create([
            'date' => Carbon::today()->toDateString(),
            'start_time' => date('H:i:s')
        ]);
        $concert = Concert::first();

        $this
            ->get($this->upcoming_route)
            ->assertJsonFragment(['date' => $concert->date()]);
    }

    public function test_upcoming_past_start_time()
    {
        $this->generate_past_concert();

        $upcoming_count = $this
            ->get($this->upcoming_route)
            ->baseResponse
            ->content();
        $past_count = $this
            ->get($this->past_route)
            ->baseResponse
            ->content();

        $this->assertCount(0, json_decode($upcoming_count, true));
        $this->assertCount(1, json_decode($past_count, true));
    }

    public function test_upcoming_no_concerts()
    {
        $this
            ->get($this->upcoming_route)
            ->assertJsonStructure(['*' => []]);
    }

    public function test_all_no_concerts()
    {
        $this
            ->get($this->all_route)
            ->assertJsonStructure(['*' => []]);
    }

    public function test_past_no_concerts()
    {
        $this
            ->get($this->past_route)
            ->assertJsonStructure(['*' => []]);
    }

    public function test_storing_concert()
    {
        $this
            ->assertDatabaseCount(Concert::class, 0)
            ->assertDatabaseCount(Band::class, 0)
            ->assertDatabaseCount(Place::class, 0);
        $this
            ->post($this->store_route, $this->concert_data, $this->auth_header())
            ->assertCreated()
            ->assertJsonStructure(['message']);
        $this
            ->assertDatabaseCount(Concert::class, 1)
            ->assertDatabaseCount(Band::class, 1)
            ->assertDatabaseCount(Place::class, 1);
    }

    public function test_storing_concert2()
    {
        $band = Band::create(['name' => 'test']);
        $this->concert_data['band_name'] = $band->name;

        $this
            ->post($this->store_route, $this->concert_data, $this->auth_header())
            ->assertCreated();
    }

    public function test_storing_concert3()
    {
        $band = Band::create(['name' => 'test']);
        $location = Place::create(['plz' => 10002, 'name' => 'test']);
        $this->concert_data['band_name'] = $band->name;
        $this->concert_data['location']['plz'] = $location->plz;
        $this->concert_data['location']['name'] = $location->name;

        $this
            ->post($this->store_route, $this->concert_data, $this->auth_header())
            ->assertCreated();
    }

    public function test_auth_for_creating_concerts()
    {
        $this
            ->post($this->store_route, headers: $this->accept_header())
            ->assertUnauthorized()
            ->assertJsonStructure(['message']);
    }

    public function test_invalid_date_format()
    {
        $date_formats = ['12.08.2022', '12-Aug-2022'];
        foreach ($date_formats as $date_format) {
            $this->concert_data['date'] = $date_format;
            $this
                ->post($this->store_route, $this->concert_data, $this->auth_header())
                ->assertStatus(400)
                ->assertJsonStructure(['error', 'message']);
        }
    }

    public function test_invalid_time_format()
    {
        $time_formats = ['12:10', '10:12 am', '5:3:1', '12.04.06'];
        foreach ($time_formats as $time_format) {
            $this->concert_data['start_time'] = $time_format;
            $this
                ->post($this->store_route, $this->concert_data, $this->auth_header())
                ->assertStatus(400)
                ->assertJsonStructure(['error', 'message']);
        }
        foreach ($time_formats as $time_format) {
            $this->concert_data['end_time'] = $time_format;
            $this
                ->post($this->store_route, $this->concert_data, $this->auth_header())
                ->assertStatus(400)
                ->assertJsonStructure(['error', 'message']);
        }
    }

    public function test_invalid_plz()
    {
        $plzs = [10, 200, 1290923489209348, '12345'];
        foreach ($plzs as $plz) {
            $this->concert_data['location']['plz'] = $plz;
            $this
                ->post($this->store_route, $this->concert_data, $this->auth_header())
                ->assertStatus(400)
                ->assertJsonStructure(['error', 'message']);
        }
    }

    public function test_integer_as_string()
    {
        $this->concert_data['location']['plz'] = '12345';
        $this
            ->post($this->store_route, $this->concert_data, $this->auth_header())
            ->assertStatus(400);
    }

    private function generate_future_concert(): void
    {
        $time = new DateTime();
        $time->modify('+2 hours');
        Concert::factory()->create([
            'date' => Carbon::today()->toDateString(),
            'start_time' => $time->format('H:i:s'),
        ]);
    }

    private function generate_past_concert(): void
    {
        $time = new DateTime();
        $time->modify('-2 hours');
        Concert::factory()->create([
            'date' => Carbon::yesterday()->toDateString(),
            'start_time' => $time->format('H:i:s'),
        ]);
    }
}
