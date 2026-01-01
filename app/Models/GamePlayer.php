<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GamePlayer extends Model
{
    protected $fillable = ['game_id', 'user_id', 'display_name', 'seat', 'is_starting_player'];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}



