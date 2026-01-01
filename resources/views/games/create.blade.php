<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Neues Dart-Spiel</h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto px-4">
        <form method="POST" action="{{ route('games.store') }}" class="space-y-4 bg-white p-6 rounded shadow">
           @csrf

<div>
    <label class="block font-medium">Startscore</label>
    <select name="start_score" class="border rounded w-full p-2">
        <option value="501" selected>501</option>
        <option value="301">301</option>
    </select>
</div>

<div>
    <label class="block font-medium">Legs (First to)</label>
    <select name="legs_to_win" class="border rounded w-full p-2">
        <option value="3" selected>First to 3</option>
        <option value="5">First to 5</option>
        <option value="7">First to 7</option>
        <option value="9">First to 9</option>
        <option value="11">First to 11</option>
        <option value="13">First to 13</option>
    </select>
</div>

<div>
    <label class="block font-medium">Sets (First to)</label>
    <select name="sets_to_win" class="border rounded w-full p-2">
        <option value="0" selected>Keine Sets (nur Legs)</option>
        <option value="3">First to 3 Sets</option>
        <option value="4">First to 4 Sets</option>
        <option value="5">First to 5 Sets</option>
        <option value="6">First to 6 Sets</option>
    </select>
</div>


<div>
    <label class="block font-medium">In-Regel</label>
    <select name="in_rule" class="border rounded w-full p-2">
        <option value="straight" selected>Straight In</option>
        <option value="double">Double In</option>
        <option value="master">Master In</option>
    </select>
</div>

<div>
    <label class="block font-medium">Out-Regel</label>
    <select name="out_rule" class="border rounded w-full p-2">
        <option value="single">Single Out</option>
        <option value="double" selected>Double Out</option>
        <option value="master">Master Out</option>
    </select>
</div>

<div class="flex items-center gap-2">
    <input id="bull_50" name="bull_50" type="checkbox" value="1" checked>
    <label for="bull_50" class="font-medium">Bull zählt 50 (Single Bull bleibt 25)</label>
</div>

<hr class="my-4">

<div>
    <label class="block font-medium">Gegner (Name)</label>
    <input name="opponent_name" type="text" value="Gast" class="border rounded w-full p-2">
</div>

<div>
    <label class="block font-medium">Wer beginnt?</label>
    <select name="starting_seat" class="border rounded w-full p-2">
        <option value="1" selected>Ich</option>
        <option value="2">Gegner</option>
    </select>
</div>

<x-primary-button>
    Spiel starten
</x-primary-button>

@if ($errors->any())
    <div class="text-red-600">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
@endif

        </form>
    </div>
</x-app-layout>
