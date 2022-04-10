<?php

namespace App\Http\Controllers;

use App\Http\Resources\ConcertResource;
use App\Models\Concert;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

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
}
