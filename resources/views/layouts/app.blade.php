<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'BuTer Doorprize')</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        html,
        body {
            margin: 0;
            min-height: 100%;
        }

        body {
            min-height: 100vh;

            background-image:
                linear-gradient(
                    rgba(0, 0, 0, 0.20),
                    rgba(0, 0, 0, 0.20)
                ),
                url('{{ asset('images/background.png') }}');

            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
    </style>

    @stack('styles')
</head>
<body>
    @auth
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ auth()->user()->canManageDraw() ? route('admin.dashboard') : route('participant.dashboard') }}">BuTer Lucky Draw</a>
            <div class="d-flex align-items-center gap-3">
                @if(auth()->user()->canManageDraw())
                    <a href="{{ route('admin.dashboard') }}" class="text-light text-decoration-none">Dashboard</a>
                    <a href="{{ route('admin.prizes.index') }}" class="text-light text-decoration-none">Hadiah</a>
                    <a href="{{ route('admin.draw.index') }}" class="text-light text-decoration-none">Pengundian</a>
                @endif
                <span class="text-light small">{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-sm btn-outline-light">Logout</button>
                </form>
            </div>
        </div>
    </nav>
    @endauth

    <div class="container pb-5">

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
