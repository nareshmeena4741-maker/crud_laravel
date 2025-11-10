@extends('layouts.app')
@section('content')
<a class="btn btn-primary mb-3" href="{{ route('tournaments.create') }}">Create Tournament</a>

<table class="table table-bordered">
    <thead><tr><th>#</th><th>Name</th><th>Team Size</th><th>Teams Added</th><th>Action</th></tr></thead>
    <tbody>
    @foreach($tournaments as $t)
        <tr>
            <td>{{ $t->id }}</td>
            <td>{{ $t->name }}</td>
            <td>{{ $t->team_size }}</td>
            <td>{{ $t->teams_count }}</td>
            <td>
                <a class="btn btn-sm btn-info" href="{{ route('teams.index',$t->id) }}">Teams</a>
                <a class="btn btn-sm btn-success" href="{{ route('results.show',$t->id) }}">Result</a>
                <a class="btn btn-sm btn-warning" href="{{ route('tournaments.edit',$t->id) }}">Edit</a>

                <form style="display:inline" method="POST" action="{{ route('tournaments.destroy',$t->id) }}">
                    @csrf
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete tournament?')">Delete</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
