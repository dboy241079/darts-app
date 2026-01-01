<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Turn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TurnController extends Controller
{
    public function store(Request $request, Game $game)
    {
        abort_unless($game->created_by === $request->user()->id, 403);
        abort_if($game->status === 'finished', 400, 'Spiel ist beendet.');

        $data = $request->validate([
            'score' => ['required', 'integer', 'min:0', 'max:180'],
        ]);

        return DB::transaction(function () use ($data, $game) {

            $settings = $game->settings ?? [];
            $state = $game->state ?? [];

            $startScore = (int)($settings['start_score'] ?? 501);

            $state['sets'] = $state['sets'] ?? [1 => 0, 2 => 0];
            $state['legs'] = $state['legs'] ?? [1 => 0, 2 => 0];
            $state['remaining'] = $state['remaining'] ?? [1 => $startScore, 2 => $startScore];

            $currentSeat = (int)($state['current_seat'] ?? 1);

            $remainingBefore = (int)($state['remaining'][$currentSeat] ?? $startScore);
            $score = (int)$data['score'];

            $calcAfter = $remainingBefore - $score;

            $isBust = false;
            $isFinish = false;
            $remainingAfter = $calcAfter;

            // Etappe 1: Bust nur wenn unter 0
            if ($calcAfter < 0) {
                $isBust = true;
                $remainingAfter = $remainingBefore;
            } elseif ($calcAfter === 0) {
                $isFinish = true;
            }

            $nextTurnNo = (int)($game->turns()->max('turn_no') ?? 0) + 1;

            Turn::create([
                'game_id' => $game->id,
                'seat' => $currentSeat,
                'turn_no' => $nextTurnNo,
                'score' => $score,
                'remaining_before' => $remainingBefore,
                'remaining_after' => $remainingAfter,
                'is_bust' => $isBust,
                'is_finish' => $isFinish,
            ]);

            // State update
            if ($isFinish) {
                $format = $settings['format'] ?? 'legs';

                if ($format === 'sets') {
                    $legsTarget = (int)($settings['legs_per_set_to_win'] ?? 3);
                    $setsTarget = (int)($settings['sets_to_win'] ?? 3);

                    $state['legs'][$currentSeat] = (int)($state['legs'][$currentSeat] ?? 0) + 1;

                    if ($state['legs'][$currentSeat] >= $legsTarget) {
                        $state['sets'][$currentSeat] = (int)($state['sets'][$currentSeat] ?? 0) + 1;
                        $state['legs'] = [1 => 0, 2 => 0];

                        $state['set_no'] = (int)($state['set_no'] ?? 1) + 1;
                        $state['leg_no_in_set'] = 1;

                        if ($state['sets'][$currentSeat] >= $setsTarget) {
                            $game->status = 'finished';
                            $game->ended_at = now();
                        }
                    } else {
                        $state['leg_no_in_set'] = (int)($state['leg_no_in_set'] ?? 1) + 1;
                    }
                } else {
                    $legsTarget = (int)($settings['legs_to_win'] ?? 3);

                    $state['legs'][$currentSeat] = (int)($state['legs'][$currentSeat] ?? 0) + 1;
                    $state['leg_no_in_set'] = (int)($state['leg_no_in_set'] ?? 1) + 1;

                    if ($state['legs'][$currentSeat] >= $legsTarget) {
                        $game->status = 'finished';
                        $game->ended_at = now();
                    }
                }

                // neues Leg startet → remaining reset
                $state['remaining'] = [1 => $startScore, 2 => $startScore];

                // Startspieler nächstes Leg: alternierend
                $legStart = (int)($state['leg_starting_seat'] ?? ($state['current_seat'] ?? 1));
                $legStart = 3 - $legStart;

                $state['leg_starting_seat'] = $legStart;
                $state['current_seat'] = $legStart;

            } else {
                $state['remaining'][$currentSeat] = $remainingAfter;
                $state['current_seat'] = 3 - $currentSeat;
            }

            $game->state = $state;
            $game->save();

            return redirect()->route('games.show', $game);
        });
    }
}
