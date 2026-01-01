<x-app-layout>
    @php
        $p1 = $game->players->firstWhere('seat', 1);
        $p2 = $game->players->firstWhere('seat', 2);

        $format = $game->settings['format'] ?? 'legs';

        $legsTarget = $format === 'sets'
            ? (int)($game->settings['legs_per_set_to_win'] ?? 3)
            : (int)($game->settings['legs_to_win'] ?? 3);

        $legs1 = (int)($game->state['legs'][1] ?? 0);
        $legs2 = (int)($game->state['legs'][2] ?? 0);

        $rem1 = (int)($game->state['remaining'][1] ?? ($game->settings['start_score'] ?? 501));
        $rem2 = (int)($game->state['remaining'][2] ?? ($game->settings['start_score'] ?? 501));

        $currentSeat = (int)($game->state['current_seat'] ?? 1);

        // letzte 3 turns pro seat (neuste oben)
        $t1 = ($turns ?? collect())->where('seat', 1)->sortByDesc('turn_no')->take(3);
        $t2 = ($turns ?? collect())->where('seat', 2)->sortByDesc('turn_no')->take(3);

        // Styling-Idee: >170 rot (kein Checkout in 3 Darts), sonst neutral
        $seat2Danger = $rem2 > 170;
    @endphp

    <div class="min-h-screen bg-slate-800 text-white">
        {{-- Topbar (Platzhalter wie im Screen) --}}
        <div class="text-xs text-slate-300 px-4 py-2 border-b border-white/10">
            <span class="hover:text-white cursor-pointer">Spiel verlassen</span>
            <span class="mx-2">|</span>
            <span class="hover:text-white cursor-pointer">Spiel abbrechen</span>
            <span class="mx-2">|</span>
            <span class="hover:text-white cursor-pointer">Tastatur ein/aus</span>
            <span class="mx-2">|</span>
            <span class="hover:text-white cursor-pointer">Statistiken</span>
        </div>

        <div class="max-w-6xl mx-auto px-4 py-8 space-y-10">

            {{-- Player Cards + Match Info --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                {{-- Seat 1 --}}
                <div class="space-y-3">
                    <div class="text-center text-2xl font-semibold">
                        {{ $p1->display_name ?? 'dboy' }}
                    </div>

                    <div class="rounded-lg border border-white/10 bg-slate-700/40 p-4">
                        <div class="flex items-center justify-center gap-3">
                            {{-- Play-Icon wenn am Zug --}}
                            <div class="h-8 w-8 grid place-items-center rounded bg-white/10">
                                @if($currentSeat === 1)
                                    <svg viewBox="0 0 24 24" class="h-5 w-5 fill-white">
                                        <path d="M8 5v14l11-7z"></path>
                                    </svg>
                                @else
                                    <span class="text-white/40 text-xs"> </span>
                                @endif
                            </div>

                            <div class="text-6xl font-extrabold tracking-tight">
                                {{ $rem1 }}
                            </div>
                        </div>
                    </div>

                    {{-- History Seat 1 --}}
                    <div class="space-y-2">
                        @foreach($t1 as $turn)
                            <div class="flex items-center justify-center gap-6">
                                <div class="text-4xl font-bold">{{ $turn->score }}</div>
                                <div class="text-lg text-white/50 w-10 text-right">
                                    {{ $turn->turn_no * 3 }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Center Match Info + Keypad --}}
                <div class="space-y-6">

                    <div class="text-center text-sm text-white/70">
                        Best of {{ $legsTarget }} Legs
                    </div>

                    <div class="mx-auto w-fit rounded-lg overflow-hidden border border-white/10">
                        <div class="grid grid-cols-3 text-center">
                            <div class="bg-cyan-700/90 px-6 py-3 font-bold text-2xl">{{ $legs1 }}</div>
                            <div class="bg-cyan-800/90 px-6 py-3 text-sm font-semibold">Legs</div>
                            <div class="bg-cyan-700/90 px-6 py-3 font-bold text-2xl">{{ $legs2 }}</div>
                        </div>
                    </div>

                    {{-- Input + “Undo” placeholder --}}
                    <div class="text-center space-y-2">
                        <input id="scoreInput" name="score" type="number" min="0" max="180" value="60"
                               class="mx-auto w-24 text-center text-xl rounded bg-white text-slate-900 px-3 py-2" />

                        <button disabled
                            class="block mx-auto px-4 py-2 rounded border border-cyan-500/60 text-cyan-200/60 cursor-not-allowed">
                            Score rückgängig (später)
                        </button>
                    </div>

                    {{-- Keypad --}}
                    <form id="turnForm" method="POST" action="{{ route('games.turns.store', $game) }}" class="mx-auto w-fit">
                        @csrf
                        <input type="hidden" name="score" id="scoreHidden" value="60">

                        <div class="rounded-lg overflow-hidden bg-slate-600/50 border border-white/10">
                            <div class="grid grid-cols-3 gap-px bg-white/10">
                                @foreach([1,2,3,4,5,6,7,8,9] as $n)
                                    <button type="button"
                                        class="h-14 w-20 bg-slate-600/60 hover:bg-slate-500/60 text-xl font-semibold"
                                        onclick="appendDigit({{ $n }})">
                                        {{ $n }}
                                    </button>
                                @endforeach

                                <button type="button"
                                    class="h-14 w-20 bg-red-600/80 hover:bg-red-600 text-xl font-bold"
                                    onclick="clearScore()">
                                    ✕
                                </button>

                                <button type="button"
                                    class="h-14 w-20 bg-slate-600/60 hover:bg-slate-500/60 text-xl font-semibold"
                                    onclick="appendDigit(0)">
                                    0
                                </button>

                                <button type="submit"
                                    class="h-14 w-20 bg-emerald-600/80 hover:bg-emerald-600 text-xl font-bold">
                                    ✓
                                </button>
                            </div>
                        </div>

                        @if ($errors->any())
                            <div class="mt-3 text-red-300 text-sm text-center">
                                @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
                            </div>
                        @endif
                    </form>

                    <div class="text-xs text-white/50 text-center">
                        Am Zug: Seat {{ $currentSeat }}
                    </div>
                </div>

                {{-- Seat 2 --}}
                <div class="space-y-3">
                    <div class="text-center text-2xl font-semibold">
                        {{ $p2->display_name ?? 'Lokaler Gast' }}
                    </div>

                    <div class="rounded-lg border border-white/10 p-4"
                         class="bg-slate-700/40"
                         style="background: {{ $seat2Danger ? 'rgba(220,38,38,0.85)' : 'rgba(51,65,85,0.6)' }};">
                        <div class="flex items-center justify-center">
                            <div class="text-6xl font-extrabold tracking-tight">
                                {{ $rem2 }}
                            </div>
                        </div>
                    </div>

                    {{-- History Seat 2 --}}
                    <div class="space-y-2">
                        @foreach($t2 as $turn)
                            <div class="flex items-center justify-center gap-6">
                                <div class="text-lg text-white/50 w-10 text-right">
                                    {{ $turn->turn_no * 3 }}
                                </div>
                                <div class="text-4xl font-bold">{{ $turn->score }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- Bottom Stats placeholder --}}
            <div class="bg-slate-700/40 border border-white/10 rounded-lg px-4 py-3 text-xs text-white/70">
                Stats-Bar (kommt später): Leg Avg / Match Avg / First 9 / 100+ / 140+ / 180 / High Finish / Short Leg …
            </div>
        </div>
    </div>

    <script>
        const input = document.getElementById('scoreInput');
        const hidden = document.getElementById('scoreHidden');

        function syncHidden() {
            const v = parseInt(input.value || '0', 10);
            const clamped = Math.max(0, Math.min(180, isNaN(v) ? 0 : v));
            input.value = clamped;
            hidden.value = clamped;
        }

        function appendDigit(d) {
            // einfache Eingabe: Zahl anhängen, dann clamp auf 180
            let cur = (input.value || '').toString();
            if (cur === '0') cur = '';
            input.value = (cur + d).slice(0, 3);
            syncHidden();
        }

        function clearScore() {
            input.value = '0';
            syncHidden();
        }

        input.addEventListener('input', syncHidden);
        syncHidden();
    </script>
</x-app-layout>
