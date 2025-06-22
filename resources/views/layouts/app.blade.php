<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Aplikasi Event</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container-fluid">
        <div class="row">

            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 bg-dark position-fixed top-0 start-0 vh-100 d-flex flex-column p-3">
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
                    <!-- Sidebar Item: Data Peserta -->
                    <li class="nav-item">
                        <span class="nav-link text-white fw-bold">
                            Data Peserta
                        </span>
                        <ul class="list-unstyled ps-3">
                            @foreach ($sidebar_events ?? [] as $event)
                            <li>
                                <a class="nav-link text-white {{ request()->is("participants/event/$event->id") ? 'bg-secondary' : '' }}"
                                    href="{{ route('participants', ['eventId' => $event->id]) }}">
                                    {{ $event->nama }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </li>

                    <!-- Sidebar Item: Statistik Event -->
                    <li class="nav-item mt-2">
                        <span class="nav-link text-white fw-bold">
                            Statistik Event
                        </span>
                        <ul class="list-unstyled ps-3">
                            @foreach ($sidebar_events ?? [] as $event)
                            <li>
                                <a class="nav-link text-white {{ request()->is("participants/event/$event->id/statistic") ? 'bg-secondary' : '' }}"
                                    href="{{ route('participants.statistic', ['eventId' => $event->id]) }}">
                                    {{ $event->nama }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
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
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-auto col-lg-10 px-md-4 py-4" style="margin-left: 250px;">
                @yield('content')
            </main>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>