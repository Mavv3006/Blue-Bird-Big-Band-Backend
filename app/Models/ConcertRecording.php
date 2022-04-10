<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConcertRecording extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'concert_recordings';

    protected $fillable = [
        'file_name',
        'description',
        'size'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function concert(): BelongsTo
    {
        return $this->belongsTo(Concert::class, 'concert_date', 'date');
    }

    public function song_type(): BelongsTo
    {
        return $this->belongsTo(SongType::class, 'type', 'id');
    }
}