@extends('layouts.app')
@section('content')
<h4>Create Tournament</h4>


<form method="POST" action="{{ route('tournaments.store') }}">
    @csrf
    <div class="mb-3">
        <label>Name</label>
        <input name="name" class="form-control" value="{{ old('name') }}">
    </div>
    <div class="mb-3">
        <label>Team Size</label>
        <input name="team_size" type="number" class="form-control" value="{{ old('team_size') }}">
        <div class="form-text">Use power of two (4,8,16) for knockout.</div>
    </div>
    <button class="btn btn-success">Save</button>
        <a href="{{ route('tournaments.index') }}" class="btn btn-light">Back</a>

</form>
@endsection
