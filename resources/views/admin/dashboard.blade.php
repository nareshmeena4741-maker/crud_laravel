@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Admin Dashboard</h3>
        <a href="{{ route('admin.user.create') }}" class="btn btn-sm btn-primary">Create User</a>
    </div>

    <h5>Staff List</h5>







    <form method="GET" action="{{ route('admin.dashboard') }}" class="mb-3" style="max-width: 300px;">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search staff..."
                value="{{ request('search') }}">
            <button class="btn btn-primary">Search</button>
        </div>
    </form>





    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Active</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($staff as $s)
                <tr>
                    <td>{{ $s->id }}</td>

                    {{-- PROFILE IMAGE --}}
                    <td>
                        @if ($s->profile_image)
                            <img src="{{ asset('storage/' . $s->profile_image) }}" width="50" height="50"
                                class="rounded-circle">
                        @else
                            <span>No Image</span>
                        @endif
                    </td>

                    <td>{{ $s->name }}</td>
                    <td>{{ $s->email }}</td>
                    <td>{{ $s->phone }}</td>
                    <td>{{ $s->role }}</td>
                    <td>{{ $s->is_active ? 'Yes' : 'No' }}</td>

                    <td class="d-flex gap-2">
                        <a href="{{ route('admin.user.edit', $s->id) }}" class="btn btn-sm btn-info">Edit</a>

                        <form method="POST" action="{{ route('admin.user.delete', $s->id) }}"
                            onsubmit="return confirm('Are you sure to delete?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>

                        <form method="POST" action="{{ route('admin.user.toggle', $s->id) }}">
                            @csrf
                            <button class="btn btn-sm btn-warning" type="submit">Toggle Active</button>
                        </form>

                        <button class="btn btn-sm btn-secondary" data-bs-toggle="modal"
                            data-bs-target="#docModal{{ $s->id }}">
                            📄
                        </button>


                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>













    <div class="mt-3">
        {{ $staff->links() }}
    </div>







    @foreach ($staff as $s)
        <div class="modal fade" id="docModal{{ $s->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Documents of {{ $s->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        @if ($s->documents->count() > 0)
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>File Name</th>
                                        <th>Download</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($s->documents as $doc)
                                        <tr>
                                            <td>{{ $doc->file_type }}</td>
                                            <td>
                                                <a href="{{ asset('storage/' . $doc->file_path) }}" download
                                                    class="btn btn-sm btn-primary">
                                                    Download
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p>No documents uploaded.</p>
                        @endif

                    </div>

                </div>
            </div>
        </div>
    @endforeach


@endsection
