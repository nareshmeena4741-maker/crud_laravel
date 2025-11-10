@extends('layouts.app')
@section('content')
<h4>Edit Team for {{ $tournament->name }}</h4>
<form method="POST" action="{{ route('teams.update', [$tournament->id, $team->id]) }}">
    @csrf
    <div class="mb-3">
        <label>Team Name</label>
        <input name="team_name" class="form-control" value="{{ old('team_name', $team->team_name) }}">
    </div>
    <button class="btn btn-primary">Update</button>
    <a href="{{ route('teams.index', $tournament->id) }}" class="btn btn-secondary">Back</a>
</form>
@endsection
