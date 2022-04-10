<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Place extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'plz';
    protected $fillable = [
        'name',
        'plz'
    ];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function concerts(): HasMany
    {
        return $this->hasMany(Concert::class, 'place_plz', 'plz');
    }

    public function equals($otherLocation): bool
    {
        return ($this->id == $otherLocation->id) && ($this->name == $otherLocation->name);
    }
}
