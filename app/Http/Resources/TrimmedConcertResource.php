<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrimmedConcertResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'concert' => [
                'date' => $this->date(),
                'description' => $this->place_description,
                'place' => $this->place->name
            ],
            'files' => ConcertRecordingResource::collection($this->recordings()->getResults())
        ];
    }
}
