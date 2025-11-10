@extends('layouts.app')

@section('title','Register')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-6">
    <h3 class="mb-3">Register (Staff)</h3>

    @if($errors->any())
      <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ route('register') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input name="name" value="{{ old('name') }}" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input name="email" value="{{ old('email') }}" type="email" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input name="password" type="password" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input name="password_confirmation" type="password" class="form-control" required>
      </div>

      <button class="btn btn-success" type="submit">Register</button>
    </form>
  </div>
</div>
@endsection
