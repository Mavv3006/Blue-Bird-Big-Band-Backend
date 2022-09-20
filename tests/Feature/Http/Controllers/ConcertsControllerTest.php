<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Concert;
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
    private string $store_route = 'api/concert';

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
        $data = [
            'date' => 'test',
            'start_time' => 'test',
            'end_time' => 'test',
            'band_name' => 'test',
            'location' => ['street' => 'test', 'number' => 'test', 'plz' => 10002, 'name' => 'test',],
            'description' => ['place' => 'test', 'organizer' => 'test',],
        ];
        $this
            ->post($this->store_route, $data, $this->auth_header())
            ->assertStatus(201);
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
