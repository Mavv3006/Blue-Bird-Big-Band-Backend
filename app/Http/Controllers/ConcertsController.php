<?php

namespace App\Http\Controllers;

use App\Http\Resources\ConcertResource;
use App\Models\Concert;
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
                'date' => 'required|string',
                'start_time' => 'required|string',
                'end_time' => 'required|string',
                'band_name' => 'required|string',
                'location' => 'array:street,number,plz,name',
                'location.street' => 'required|string',
                'location.number' => 'required|string',
                'location.plz' => 'required|integer|min:10000|max:99999',
                'location.name' => 'required|string',
                'description' => 'array:place,organizer',
                'description.place' => 'required|string',
                'description.organizer' => 'required|string',
            ]);

            $concert = Concert::insert([
                'start_time' => '',
            ]);

            return response()->json([]);
        } catch (ValidationException $e) {
            $content = [
                'error' => 'Bad Request',
                'message' => $e->getMessage()
            ];
            return response()->json($content, 400);
        }
    }
}
