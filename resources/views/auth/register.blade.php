@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3 class="mb-3">Register (Staff)</h3>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input name="name" value="{{ old('name') }}" class="form-control" required maxlength="50"
                        pattern="[A-Za-z\s]+" title="Only letters and spaces allowed">
                </div>


                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input name="email" value="{{ old('email') }}" type="email" class="form-control" required
                        maxlength="100">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input name="password" type="password" class="form-control" required minlength="6" maxlength="30">
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input name="password_confirmation" type="password" class="form-control" required minlength="6"
                        maxlength="30">
                </div>

                <button class="btn btn-success" type="submit">Register</button>
            </form>

        </div>
    </div>
@endsection
