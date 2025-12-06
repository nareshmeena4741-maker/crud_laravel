@extends('layouts.app')

@section('title', 'Create User')

@section('content')

    <div class="row">
        <div class="col-md-6">

            <h3>Create User (Admin)</h3>

            {{-- NORMAL SUBMIT FORM --}}
            <form id="createUserForm" method="POST" action="{{ route('admin.user.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label>Name</label>
                    <input name="name" class="form-control" value="{{ old('name') }}">
                    <span class="text-danger error">
                        @error('name')
                            {{ $message }}
                        @enderror
                    </span>
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input name="email" class="form-control" value="{{ old('email') }}">
                    <span class="text-danger error">
                        @error('email')
                            {{ $message }}
                        @enderror
                    </span>
                </div>

                <div class="mb-3">
                    <label>Phone</label>
                    <input name="phone" class="form-control" value="{{ old('phone') }}">
                    <span class="text-danger error">
                        @error('phone')
                            {{ $message }}
                        @enderror
                    </span>
                </div>

                <div class="mb-3">
                    <label>Role</label>
                    <select name="role" class="form-control">
                        <option value="">Select</option>
                        <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    <span class="text-danger error">
                        @error('role')
                            {{ $message }}
                        @enderror
                    </span>
                </div>

                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control">
                    <span class="text-danger error">
                        @error('password')
                            {{ $message }}
                        @enderror
                    </span>
                </div>

                <div class="mb-3">
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                    <span class="text-danger error"></span>
                </div>

                <div class="mb-3">
                    <label>Profile Image</label>
                    <input type="file" name="profile_image" class="form-control">
                    <span class="text-danger error">
                        @error('profile_image')
                            {{ $message }}
                        @enderror
                    </span>
                </div>

                <div class="mb-3">
                    <label>Documents</label>
                    <input type="file" name="documents[]" class="form-control" multiple>
                    <span class="text-danger error">
                        @error('documents')
                            {{ $message }}
                        @enderror
                    </span>
                </div>

                <button type="submit" id="submitBtn" class="btn btn-primary">Create</button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>


            </form>

        </div>
    </div>

@endsection


@section('scripts')

    <script>
        $(document).ready(function() {

            $("#createUserForm").validate({
                ignore: [],
                errorElement: "span",
                errorClass: "text-danger",

                errorPlacement: function(error, element) {
                    element.closest(".mb-3").find(".error").text(error.text());
                },

                highlight: function(element) {
                    $(element).addClass("is-invalid");
                },

                unhighlight: function(element) {
                    $(element).removeClass("is-invalid");
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
                        required: true,
                        minlength: 6
                    },
                    password_confirmation: {
                        required: true,
                        equalTo: "[name=password]"
                    },
                    profile_image: {
                        required: true,
                        extension: "jpg|jpeg|png"
                    },
                    "documents[]": {
                        required: true,
                        extension: "jpg|jpeg|png|pdf"
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
                        required: "Select role"
                    },
                    password: {
                        required: "Password required"
                    },
                    password_confirmation: {
                        equalTo: "Passwords must match"
                    },
                    profile_image: {
                        required: "Profile image required"
                    },
                    "documents[]": {
                        required: "Please upload at least one document"
                    }
                }
            });

        });
    </script>

@endsection
