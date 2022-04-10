<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SongResource extends JsonResource
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
            'title' => $this->song_name,
            'genre' => $this->genre,
            'author' => $this->author,
            'arranger' => $this->arranger,
            'file_name' => $this->file_name
        ];
    }
}
