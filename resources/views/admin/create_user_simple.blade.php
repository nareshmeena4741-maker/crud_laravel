@extends('layouts.app')

@section('title', 'Create User')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <h3>Create User (Admin)</h3>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.user.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input name="name" value="{{ old('name') }}" class="form-control" required maxlength="50"
                        pattern="[A-Za-z\s]+" title="Only letters and spaces allowed">
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input name="email" value="{{ old('email') }}" type="email" class="form-control" maxlength="100"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input name="phone" value="{{ old('phone') }}" class="form-control" pattern="[0-9]+" minlength="10"
                        maxlength="10" title="Only numbers allowed (10–15 digits)" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-control" required>
                        <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input name="password" type="password" class="form-control" required minlength="6" maxlength="10">
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input name="password_confirmation" type="password" class="form-control" required minlength="6"
                        maxlength="30">
                </div>

                <div class="mb-3">
                    <label>User Profile Image</label>
                    <input type="file" name="profile_image" accept="image/*" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Upload Documents</label>
                    <input type="file" name="documents[]" multiple class="form-control">
                </div>

                <button class="btn btn-primary" type="submit">Create</button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>

            </form>

        </div>
    </div>
@endsection
