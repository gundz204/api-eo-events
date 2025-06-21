<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Aplikasi Event</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container-fluid">
        <div class="row">

            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse min-vh-100">
                <div class="position-sticky pt-3">
                    <h5 class="text-white text-center mt-2">EventApp</h5>
                    <hr class="text-white">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('events.index') ? 'bg-secondary text-white' : 'text-white' }}"
                                href="{{ route('events.index') }}">
                                Daftar Event
                            </a>
                        </li>

                        @auth
                        @if (Auth::user()->role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('events.create') ? 'bg-secondary text-white' : 'text-white' }}"
                                href="{{ route('events.create') }}">
                                Tambah Event
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/participants*') ? 'bg-secondary text-white' : 'text-white' }}"
                                href="{{ url('/admin/participants') }}">
                                Data Peserta
                            </a>
                        </li>
                        @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('events.my_events_registered') ? 'bg-secondary text-white' : 'text-white' }}"
                                href="{{ route('events.my_events_registered') }}">
                                Tiket Event
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('events.my_events') ? 'bg-secondary text-white' : 'text-white' }}"
                                href="{{ route('events.my_events') }}">
                                Event Dihadiri
                            </a>
                        </li>
                        @endif

                        <li class="nav-item mt-3 px-3">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100">Logout</button>
                            </form>
                        </li>
                        @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('register') ? 'bg-secondary text-white' : 'text-white' }}"
                                href="{{ route('register') }}">
                                Register
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('login') ? 'bg-secondary text-white' : 'text-white' }}"
                                href="{{ route('login') }}">
                                Login
                            </a>
                        </li>
                        @endauth
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                @yield('content')
            </main>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>