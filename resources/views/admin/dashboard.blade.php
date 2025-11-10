@extends('layouts.app')

@section('title','Admin Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Admin Dashboard</h3>
  <a href="{{ route('admin.user.create') }}" class="btn btn-sm btn-primary">Create User</a>
</div>

<h5>Staff List</h5>
<table class="table table-bordered">
  <thead>
    <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Active</th><th>Action</th></tr>
  </thead>
  <tbody>
    @foreach($staff as $s)
      <tr>
        <td>{{ $s->id }}</td>
        <td>{{ $s->name }}</td>
        <td>{{ $s->email }}</td>
        <td>{{ $s->phone }}</td>
        <td>{{ $s->role }}</td>
        <td>{{ $s->is_active ? 'Yes' : 'No' }}</td>
        <td>
          <form method="POST" action="{{ route('admin.user.toggle', $s->id) }}">
            @csrf
            <button class="btn btn-sm btn-warning" type="submit">Toggle Active</button>
          </form>
        </td>
      </tr>
    @endforeach
  </tbody>
</table>
@endsection
