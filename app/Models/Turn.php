<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turn extends Model
{
    protected $fillable = [
    'game_id','seat','turn_no','score',
    'remaining_before','remaining_after',
    'is_bust','is_finish'
];
}
