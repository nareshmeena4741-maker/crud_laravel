@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="col-md-6 mx-auto">
    <h3>Edit User</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.user.update', $user->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Name</label>
            <input class="form-control" name="name" value="{{ $user->name }}" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input class="form-control" name="email" value="{{ $user->email }}">
        </div>

        <div class="mb-3">
            <label>Phone</label>
            <input class="form-control" name="phone" value="{{ $user->phone }}" minlength="6" maxlength="10">
        </div>

        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-control" required>
                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Current Profile Image:</label><br>

            @if ($user->profile_image)
                <img src="{{ asset('storage/' . $user->profile_image) }}"
                     width="90" height="90" class="rounded mb-2">
            @else
                <p>No image uploaded</p>
            @endif
        </div>

        <div class="mb-3">
            <label>Upload New Image</label>
            <input type="file" name="profile_image" class="form-control">
        </div>

        <div class="mb-3">
            <label>New Password (optional)</label>
            <input type="password" name="password" class="form-control">
        </div>

        <button class="btn btn-success" type="submit">Update</button>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>

    </form>
</div>
@endsection
