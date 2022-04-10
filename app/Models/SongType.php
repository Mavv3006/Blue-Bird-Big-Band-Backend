<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SongType extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'type',
        'folder_name'
    ];

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class, 'type', 'id');
    }

    public function concert_recordings(): HasMany
    {
        return $this->hasMany(ConcertRecording::class, 'type', 'id');
    }
}
