@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <div class="col-md-6 mx-auto">

        <h3>Edit User</h3>

        <form id="editUserForm" method="POST" action="{{ route('admin.user.update', $user->id) }}"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- NAME --}}
            <div class="mb-3">
                <label>Name</label>
                <input name="name" value="{{ old('name', $user->name) }}" class="form-control">
                <span class="text-danger error">
                    @error('name')
                        {{ $message }}
                    @enderror
                </span>
            </div>

            {{-- EMAIL --}}
            <div class="mb-3">
                <label>Email</label>
                <input name="email" value="{{ old('email', $user->email) }}" class="form-control">
                <span class="text-danger error">
                    @error('email')
                        {{ $message }}
                    @enderror
                </span>
            </div>

            {{-- PHONE --}}
            <div class="mb-3">
                <label>Phone</label>
                <input name="phone" value="{{ old('phone', $user->phone) }}" class="form-control">
                <span class="text-danger error">
                    @error('phone')
                        {{ $message }}
                    @enderror
                </span>
            </div>

            {{-- ROLE --}}
            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-control">
                    <option value="">Select Role</option>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                </select>
                <span class="text-danger error">
                    @error('role')
                        {{ $message }}
                    @enderror
                </span>
            </div>

            {{-- CURRENT IMAGE --}}
            <div class="mb-3">
                <label>Current Image</label><br>
                @if ($user->profile_image)
                    <img src="{{ asset('storage/' . $user->profile_image) }}" width="80">
                @else
                    <p>No Image</p>
                @endif
            </div>

            {{-- NEW IMAGE --}}
            <div class="mb-3">
                <label>Upload New Image</label>
                <input type="file" name="profile_image" class="form-control">
                <span class="text-danger error">
                    @error('profile_image')
                        {{ $message }}
                    @enderror
                </span>
            </div>

            {{-- PASSWORD --}}
            <div class="mb-3">
                <label>New Password (optional)</label>
                <input type="password" name="password" class="form-control">
                <span class="text-danger error">
                    @error('password')
                        {{ $message }}
                    @enderror
                </span>
            </div>

            {{-- CONFIRM PASSWORD --}}
            <div class="mb-3">
                <label>Confirm New Password</label>
                <input type="password" name="password_confirmation" class="form-control">
                <span class="text-danger error">
                    @error('password_confirmation')
                        {{ $message }}
                    @enderror
                </span>
            </div>

            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>

        </form>

    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            $("#editUserForm").validate({

                ignore: [],
                errorElement: "span",
                errorClass: "text-danger",

                errorPlacement: function(error, element) {
                    element.closest(".mb-3").find(".error").html(error);
                },

                highlight: function(el) {
                    $(el).addClass("is-invalid");
                },

                unhighlight: function(el) {
                    $(el).removeClass("is-invalid");
                    $(el).closest(".mb-3").find(".error").html("");
                },

                rules: {
                    name: {
                        required: true,
                        minlength: 2
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    phone: {
                        required: true,
                        digits: true,
                        minlength: 10,
                        maxlength: 10
                    },
                    role: {
                        required: true
                    },
                    password: {
                        minlength: 6
                    },
                    password_confirmation: {
                        equalTo: "[name=password]"
                    },
                    profile_image: {
                        extension: "jpg|jpeg|png"
                    }
                },

                messages: {
                    name: {
                        required: "Name required"
                    },
                    email: {
                        required: "Email required"
                    },
                    phone: {
                        required: "Phone required"
                    },
                    role: {
                        required: "Role required"
                    },
                    password_confirmation: {
                        equalTo: "Passwords must match"
                    },
                    profile_image: {
                        extension: "Only JPG/PNG allowed"
                    }
                },

                submitHandler: function(form) {
                    form.submit();
                }
            });

        });
    </script>
@endsection
