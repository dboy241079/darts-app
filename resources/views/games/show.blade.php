<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Spiel #{{ $game->id }}</h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto px-4 space-y-4">
        <div class="bg-white p-6 rounded shadow">
    <div class="font-semibold mb-2">Settings</div>

    <div>Startscore: {{ $game->settings['start_score'] }}</div>

    @if(($game->settings['format'] ?? 'legs') === 'sets')
    <div>Sets: First to {{ $game->settings['sets_to_win'] }}</div>
    <div>Legs pro Set: First to {{ $game->settings['legs_per_set_to_win'] }}</div>
@else
    <div>Legs: First to {{ $game->settings['legs_to_win'] }}</div>
@endif


    <div>In: {{ $game->settings['in_rule'] }}</div>
    <div>Out: {{ $game->settings['out_rule'] }}</div>
    <div>Bull: {{ ($game->settings['bull_50'] ?? true) ? '50' : '25' }} (Single Bull 25)</div>
</div>


        <div class="bg-white p-6 rounded shadow">
            <div class="font-semibold mb-2">Spieler</div>
            <ul class="list-disc pl-5">
                @foreach($game->players->sortBy('seat') as $p)
                    <li>
                        Seat {{ $p->seat }}: {{ $p->display_name }}
                        @if($p->is_starting_player) <span class="text-green-700">(beginnt)</span> @endif
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="bg-white p-6 rounded shadow">
    <div class="bg-white p-6 rounded shadow">
    <div class="font-semibold mb-2">Scoreboard</div>
    <div>Set: {{ $game->state['set_no'] ?? 1 }} | Leg im Set: {{ $game->state['leg_no_in_set'] ?? 1 }}</div>
    <div>Sets: {{ $game->state['sets'][1] ?? 0 }} - {{ $game->state['sets'][2] ?? 0 }}</div>
    <div>Legs: {{ $game->state['legs'][1] ?? 0 }} - {{ $game->state['legs'][2] ?? 0 }}</div>
    <div>Rest: {{ $game->state['remaining'][1] ?? 0 }} - {{ $game->state['remaining'][2] ?? 0 }}</div>
    <div>Am Zug: Seat {{ $game->state['current_seat'] ?? 1 }}</div>
</div>


    </div>
</x-app-layout>
