<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GamePlayer;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $games = Game::where('created_by', $request->user()->id)
            ->latest()
            ->get();

        return view('games.index', compact('games'));
    }

    public function create()
    {
        return view('games.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'start_score' => ['required', 'integer', 'in:301,501'],
            'legs_to_win' => ['required', 'integer', 'in:3,5,7,9,11,13'],
            'sets_to_win' => ['required', 'integer', 'in:0,3,4,5,6'],
            'out_rule'    => ['required', 'in:single,double,master'],
            'in_rule'     => ['required', 'in:straight,double,master'],
            'bull_50'     => ['nullable', 'boolean'],
            'opponent_name' => ['required', 'string', 'max:40'],
            'starting_seat' => ['required', 'integer', 'in:1,2'],
        ]);

        $setsToWin = (int)$data['sets_to_win'];
        $legsToWin = (int)$data['legs_to_win'];

        $settings = [
            'start_score' => (int)$data['start_score'],
            'out_rule'    => $data['out_rule'],
            'in_rule'     => $data['in_rule'],
            'bull_50'     => (bool)($data['bull_50'] ?? false),
        ];

        if ($setsToWin === 0) {
            $settings['format'] = 'legs';
            $settings['sets_to_win'] = 0;
            $settings['legs_to_win'] = $legsToWin;
        } else {
            $settings['format'] = 'sets';
            $settings['sets_to_win'] = $setsToWin;
            $settings['legs_per_set_to_win'] = $legsToWin;
        }

        $startScore = (int)$settings['start_score'];
        $startingSeat = (int)$data['starting_seat'];

        $state = [
            'leg_starting_seat' => $startingSeat,
            'set_no' => 1,
            'leg_no_in_set' => 1,
            'sets' => [1 => 0, 2 => 0],
            'legs' => [1 => 0, 2 => 0],
            'remaining' => [1 => $startScore, 2 => $startScore],
            'current_seat' => $startingSeat,
        ];

        $game = Game::create([
            'created_by' => $request->user()->id,
            'status' => 'active',
            'settings' => $settings,
            'state' => $state,
            'started_at' => now(),
        ]);

        GamePlayer::create([
            'game_id' => $game->id,
            'user_id' => $request->user()->id,
            'display_name' => $request->user()->name,
            'seat' => 1,
            'is_starting_player' => ($startingSeat === 1),
        ]);

        GamePlayer::create([
            'game_id' => $game->id,
            'user_id' => null,
            'display_name' => $data['opponent_name'],
            'seat' => 2,
            'is_starting_player' => ($startingSeat === 2),
        ]);

        return redirect()->route('games.show', $game);
    }

    public function show(Game $game, Request $request)
    {
        abort_unless($game->created_by === $request->user()->id, 403);

        $game->load('players');
        return view('games.show', compact('game'));

        $turns = $game->turns()->orderBy('turn_no', 'desc')->take(20)->get()->reverse();
return view('games.show', compact('game', 'turns'));

        $game->load('players');

// letzte 30 turns reichen fürs UI
$turns = $game->turns()->orderBy('turn_no', 'desc')->take(30)->get();

return view('games.show', compact('game', 'turns'));


    }
}
