<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Song extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'file_name',
        'song_name',
        'genre',
        'arranger',
        'size',
        'type'
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(SongType::class, 'type', 'id');
    }
}
