@extends('layouts.app')

@section('title','Create User')

@section('content')
<div class="row">
  <div class="col-md-6">
    <h3>Create User (Admin)</h3>

    @if($errors->any())
      <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ route('admin.user.store') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input name="name" value="{{ old('name') }}" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Email (optional)</label>
        <input name="email" value="{{ old('email') }}" type="email" class="form-control">
      </div>

      <div class="mb-3">
        <label class="form-label">Phone (optional)</label>
        <input name="phone" value="{{ old('phone') }}" class="form-control">
      </div>

      <div class="mb-3">
        <label class="form-label">Role</label>
        <select name="role" class="form-control" required>
          <option value="staff" {{ old('role')=='staff' ? 'selected' : '' }}>Staff</option>
          <option value="admin" {{ old('role')=='admin' ? 'selected' : '' }}>Admin</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input name="password" type="password" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input name="password_confirmation" type="password" class="form-control" required>
      </div>

      <button class="btn btn-primary" type="submit">Create</button>
    </form>
  </div>
</div>
@endsection
