@extends('layouts.app')
@section('content')
<a href="{{ route('tournaments.index') }}" class="btn btn-light mb-3">Back</a>
<h4>{{ $tournament->name }} — Results</h4>

<div class="mb-2">
    <form method="POST" action="{{ route('results.generate', $tournament->id) }}" style="display:inline;">
        @csrf
        <button class="btn btn-primary">Generate Random Results</button>
    </form>
</div>

@if(empty($bracket))
    <div class="alert alert-info">No results generated yet. Click "Generate Random Results".</div>
@endif

@if(!empty($bracket))
    <div class="row">
        {{-- For each round -> a column --}}
        @foreach($bracket as $rIndex => $matches)
            <div class="col round-column">
                <h6 class="text-center">Round {{ $rIndex + 1 }}</h6>
                @foreach($matches as $m)
                    <div class="match-box">
                        <div>{{ $m['team1'] }}</div>
                        <div class="text-muted">vs</div>
                        <div>{{ $m['team2'] }}</div>
                        <hr class="my-1">
                        <div><strong>Winner: {{ $m['winner'] }}</strong></div>
                    </div>
                @endforeach
            </div>
        @endforeach

        {{-- Final Winner column --}}
        <div class="col round-column">
            <h6 class="text-center">Champion</h6>
            @if($finalWinner)
                <div class="match-box text-center">
                    <h5>{{ $finalWinner }}</h5>
                </div>
            @else
                <div class="match-box text-center">—</div>
            @endif
        </div>
    </div>
@endif
@endsection
