<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">

        <a class="navbar-brand" href="{{ url('/') }}">
            KPI Guru 360°
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">

            <ul class="navbar-nav me-auto">

                <li class="nav-item">
                    @if(Auth::check())
                        @if(Auth::user()->role === 'guru')
                            <a class="nav-link" href="{{ route('dashboard.guru') }}">Dashboard</a>
                        @elseif(Auth::user()->role === 'kepala_sekolah')
                            <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                        @endif
                    @endif
                </li>

                {{-- MASTER DATA --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        Master Data
                    </a>
                    <ul class="dropdown-menu">

                        <li><a class="dropdown-item" href="{{ route('user.index') }}">User</a></li>
                        <li><a class="dropdown-item" href="{{ route('guru.index') }}">Guru</a></li>
                        <li><a class="dropdown-item" href="{{ route('wali-murid.index') }}">Wali Murid</a></li>
                        <li><a class="dropdown-item" href="{{ route('kpi.index') }}">KPI</a></li>
                        <li><a class="dropdown-item" href="{{ route('kpi-questions.index') }}">Pertanyaan KPI</a></li>
                        <li><a class="dropdown-item" href="{{ route('period.index') }}">Periode</a></li>
                        <li><a class="dropdown-item" href="{{ route('weights.index') }}">Bobot Penilai</a></li>
                        @if(Auth::check() && Auth::user()->role === 'kepala_sekolah')
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('recommendations.index') }}">Rekomendasi</a></li>
                        @endif

                    </ul>
                </li>

                {{-- PENILAIAN --}}
                {{-- PENILAIAN --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        Penilaian
                    </a>
                    <ul class="dropdown-menu">

                        {{-- Semua role --}}
                        <li>
                            <a class="dropdown-item" href="{{ route('evaluation.index') }}">
                                Form Penilaian
                            </a>
                        </li>

                        {{-- Semua role (opsional, bisa dibatasi) --}}
                        <li>
                            <a class="dropdown-item" href="{{ route('finalscore.index') }}">
                                Nilai Akhir 360°
                            </a>
                        </li>

                        {{-- KHUSUS KEPALA SEKOLAH --}}
                        @if(Auth::check() && Auth::user()->role === 'kepala_sekolah')
                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <a class="dropdown-item" href="{{ route('riwayat.penilaian') }}">
                                    Riwayat Penilaian
                                </a>
                            </li>
                        @endif

                    </ul>
                </li>


            </ul>

            {{-- PROFILE / LOGOUT --}}
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="nav-link">Halo, {{ Auth::user()->name ?? 'Guest' }}</span>
                </li>

                @auth
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="btn btn-outline-light btn-sm">Logout</button>
                        </form>
                    </li>
                @endauth
            </ul>

        </div>
    </div>
</nav>