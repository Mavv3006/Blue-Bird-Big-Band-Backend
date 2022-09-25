<?php

namespace App\Http\Controllers;

use App\Http\Resources\ConcertResource;
use App\Models\Band;
use App\Models\Concert;
use App\Models\Place;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class ConcertsController extends Controller
{
    public function all(): JsonResponse
    {
        $concerts = Concert::with('band', 'place')->get();
        return ConcertResource::collection($concerts)->response();
    }

    public function upcoming(): JsonResponse
    {
        $concerts = Concert::with('band', 'place')
            ->whereDate('date', '>=', Carbon::today()->toDateString())
            ->get();
        return ConcertResource::collection($concerts)->response();
    }

    public function past(): JsonResponse
    {
        $concerts = Concert::with('band', 'place')
            ->whereDate('date', '<', Carbon::today()->toDateString())
            ->get();
        return ConcertResource::collection($concerts)->response();
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'date' => 'required|date_format:Y-m-d',
                'start_time' => 'required|date_format:H:i:s',
                'end_time' => 'required|date_format:H:i:s',
                'band_name' => 'required|string',
                'location' => 'array:street,number,plz,name',
                'location.street' => 'required|string',
                'location.number' => 'required|string',
                'location.plz' => [
                    'required',
                    'integer',
                    'min:10000',
                    'max:99999',
                    function ($attribute, $value, $fail) {
                        if (!is_int($value)) {
                            $fail('The ' . $attribute . ' must be an integer.');
                        }
                    }],
                'location.name' => 'required|string',
                'description' => 'array:place,organizer',
                'description.place' => 'required|string',
                'description.organizer' => 'required|string',
            ]);

            $band = $this->getBand($data['band_name']);
            $place_plz = $this->getPlacePlz($data['location']);

            $concert = Concert::create([
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'place_street' => $data['location']['street'],
                'place_number' => $data['location']['number'],
                'place_description' => $data['description']['place'],
                'organizer_description' => $data['description']['organizer'],
                'band_id' => $band->id,
                'date' => $data['date'],
                'place_plz' => $place_plz->plz,
            ]);

            $message = 'A new concert for the ' . $concert->date . ' has been created.';
            return response()->json(['message' => $message], 201);
        } catch (ValidationException $e) {
            $content = [
                'error' => 'Bad Request',
                'message' => $e->getMessage()
            ];
            return response()->json($content, 400);
        }
    }

    private function getBand(string $band_name): mixed
    {
        $band = Band::where('name', '=', $band_name)->first();
        if ($band) return $band;
        return Band::create(['name' => $band_name]);
    }

    private function getPlacePlz(array $location): mixed
    {
        $place_plz = Place::where('plz', '=', $location['plz'])->first();
        if ($place_plz) return $place_plz;
        return Place::create([
            'plz' => $location['plz'],
            'name' => $location['name']
        ]);
    }
}
