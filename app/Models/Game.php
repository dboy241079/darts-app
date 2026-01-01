<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['created_by', 'status', 'settings', 'state', 'started_at', 'ended_at'];

   protected $casts = [
    'settings' => 'array',
    'state' => 'array',
    'started_at' => 'datetime',
    'ended_at' => 'datetime',

];


    public function players()
    {
        return $this->hasMany(GamePlayer::class);
    }
}

