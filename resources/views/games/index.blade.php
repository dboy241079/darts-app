<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl">Meine Spiele</h2>
            <a href="{{ route('games.create') }}" class="underline">+ Neues Spiel</a>
        </div>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto px-4">
        <div class="bg-white rounded shadow divide-y">
            @forelse($games as $g)
                <a href="{{ route('games.show', $g) }}" class="block p-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="font-semibold">Spiel #{{ $g->id }}</div>

                            @if($g->status === 'active')
                                <span class="text-xs px-2 py-1 rounded bg-green-100 text-green-800">active</span>
                            @elseif($g->status === 'finished')
                                <span class="text-xs px-2 py-1 rounded bg-gray-200 text-gray-800">finished</span>
                            @else
                                <span class="text-xs px-2 py-1 rounded bg-yellow-100 text-yellow-800">
                                    {{ $g->status }}
                                </span>
                            @endif
                        </div>

                        <div class="text-sm text-gray-500 text-right">
    <div>Zuletzt gespielt: {{ $g->updated_at->format('d.m.Y H:i') }}</div>
    <div class="text-xs">Erstellt: {{ $g->created_at->format('d.m.Y H:i') }}</div>
</div>

                    </div>

                    <div class="text-sm text-gray-600 mt-1">
                        {{ $g->settings['start_score'] ?? '—' }}
                        |
                        @if(($g->settings['format'] ?? 'legs') === 'sets')
                            Sets FT{{ $g->settings['sets_to_win'] ?? '?' }}
                            | Legs/Set FT{{ $g->settings['legs_per_set_to_win'] ?? ($g->settings['legs_to_win'] ?? '?') }}
                        @else
                            Legs FT{{ $g->settings['legs_to_win'] ?? '?' }}
                        @endif
                        | In: {{ $g->settings['in_rule'] ?? '—' }}
                        | Out: {{ $g->settings['out_rule'] ?? '—' }}
                    </div>
                </a>
            @empty
                <div class="p-6 text-gray-600">
                    Noch keine Spiele.
                    <a class="underline" href="{{ route('games.create') }}">Erstes Spiel starten</a>.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
