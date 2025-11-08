@extends('layouts.app')
@section('content')
<h4>Edit Tournament</h4>
<form method="POST" action="{{ route('tournaments.update') }}">
    @csrf
    <div class="mb-3">
        <label>Name</label>
        <input name="name" class="form-control" value="{{ old('name', $tournament->name) }}">
    </div>
    <div class="mb-3">
        <label>Team Size</label>
        <input name="team_size" type="number" class="form-control" value="{{ old('team_size', $tournament->team_size) }}">
    </div>
    <input type="hidden" name="id" class="form-control" value="{{ $tournament->id }}">

    <button class="btn btn-primary">Update</button>
    <a href="{{ route('tournaments.index') }}" class="btn btn-secondary">Back</a>
</form>
@endsection
