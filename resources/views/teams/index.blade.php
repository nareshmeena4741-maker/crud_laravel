@extends('layouts.app')
@section('content')
<a href="{{ route('tournaments.index') }}" class="btn btn-light mb-3">Back</a>
<h4>{{ $tournament->name }} — Teams ({{ $teams->count() }}/{{ $tournament->team_size }})</h4>

@if(!$disabled)
<form method="POST" action="{{ route('teams.store', $tournament->id) }}" class="mb-3">
    @csrf
    <div class="input-group">
        <input name="team_name" class="form-control" placeholder="Team name" required>
        <button class="btn btn-success">Add</button>
    </div>
</form>
@else
<div class="alert alert-info">Team limit reached. Cannot add more.</div>
@endif

<table class="table table-bordered">
    <thead><tr><th>#</th><th>Team Name</th><th>Action</th></tr></thead>
    <tbody>
    @foreach($teams as $team)
    <tr>
        <td>{{ $team->id }}</td>
        <td>{{ $team->team_name }}</td>
        <td>
            <a href="{{ route('teams.edit', [$tournament->id, $team->id]) }}" class="btn btn-sm btn-warning">Edit</a>
            <form style="display:inline" method="POST" action="{{ route('teams.destroy', $team->id) }}">
                @csrf
                <button class="btn btn-sm btn-danger" onclick="return confirm('Delete team?')">Delete</button>
            </form>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
@endsection
