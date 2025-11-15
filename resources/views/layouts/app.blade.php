    <!doctype html>
    <html>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>@yield('title', 'MyApp')</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


    </head>

    <body>
        <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">MyApp</a>

                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav ms-auto">
                        @auth
                            <li class="nav-item"><span class="nav-link">Hi, {{ auth()->user()->name }}</span></li>
                            <li class="nav-item">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-secondary" type="submit">Logout</button>
                                </form>
                            </li>
                        @else
                            <li class="nav-item"><a class="nav-link" href="{{ route('login.form') }}">Login</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('register.form') }}">Register</a></li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container">
            @if (session('success'))
                <div class="alert alert-success alert-message">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-message">{{ session('error') }}</div>
            @endif

            @yield('content')



        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>



        <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>


        <script>
            $(document).ready(function() {
                setTimeout(function() {
                    $(".alert-message").fadeOut('slow');
                }, 600);
            });
        </script>

        @yield('scripts')


    </body>

    </html>
