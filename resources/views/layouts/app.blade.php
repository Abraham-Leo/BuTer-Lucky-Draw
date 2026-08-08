<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DoorPrize Draw System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f6fa; }
        .navbar-brand { font-weight: 700; letter-spacing: .5px; }
        .stat-card { border: none; border-radius: 1rem; box-shadow: 0 2px 10px rgba(0,0,0,.06); }
        .ticket-number { font-size: 3rem; font-weight: 800; letter-spacing: .1em; }
    </style>
    @stack('styles')
</head>
<body>
    @auth
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ auth()->user()->canManageDraw() ? route('admin.dashboard') : route('participant.dashboard') }}">DPDS</a>
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
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
