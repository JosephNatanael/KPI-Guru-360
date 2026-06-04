<style>
/* ===============================
   NAVBAR GLOBAL STYLE
================================= */

.main-content {
    padding-top: 60px; /* tinggi navbar */
}

.navbar {
    background: linear-gradient(90deg, #1e3a8a, #2563eb);
    box-shadow: 0 4px 12px rgba(0,0,0,.12);
    font-size: 0.95rem;
}

.navbar-brand {
    font-weight: 700;
    letter-spacing: .4px;
}

/* ===============================
   NAV LINK
================================= */
.navbar .nav-link {
    color: rgba(255,255,255,.9) !important;
    padding: .6rem .9rem;
    border-radius: .45rem;
    transition: background .25s ease, color .25s ease;
}

.navbar .nav-link:hover,
.navbar .nav-link:focus {
    background: rgba(255,255,255,.15);
    color: #fff !important;
}

/* ===============================
   DROPDOWN MENU
================================= */
.dropdown-menu {
    border: none;
    border-radius: .6rem;
    padding: .4rem 0;
    box-shadow: 0 10px 25px rgba(0,0,0,.15);
    animation: dropdownFade .18s ease-out;
}

@keyframes dropdownFade {
    from {
        opacity: 0;
        transform: translateY(6px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.dropdown-item {
    padding: .55rem 1.1rem;
    font-size: .92rem;
    transition: background .2s ease, color .2s ease;
}

.dropdown-item:hover {
    background: rgba(37, 99, 235, .08);
    color: #1e3a8a;
}

/* ===============================
   HIGHLIGHT NILAI AKHIR 360°
================================= */
.dropdown-item.final-score {
    font-weight: 600;
    color: #1e40af;
    background: linear-gradient(
        90deg,
        rgba(245,158,11,.15),
        rgba(251,191,36,.15)
    );
}

.dropdown-item.final-score::after {
    content: "360°";
    font-weight: 700;
    color: #f59e0b;
    margin-left: 6px;
}

/* ===============================
   USER DROPDOWN (RIGHT)
================================= */
#navbarDropdownUser {
    font-weight: 500;
}

/* ===============================
   MOBILE ADJUSTMENT
================================= */
    .navbar-nav .nav-link {
        padding: .6rem;
    }
}

.navbar-period-badge {
    font-size: 0.72rem;
}

@media (min-width: 992px) {
    .navbar-period-badge {
        font-size: 0.88rem;
    }
}
</style>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container d-flex align-items-center justify-content-between">

        {{-- BRAND (Kiri) --}}
        <a class="navbar-brand me-0" href="{{ url('/') }}">
            KPI Guru 360°
        </a>

        {{-- PERIODE (Tengah) --}}
        @if(isset($activePeriod))
        <div class="ms-1 ms-lg-2 order-2 order-lg-1 mx-auto mx-lg-0">
            <span class="badge bg-white bg-opacity-10 text-white-50 border border-white border-opacity-10 fw-normal px-2 px-lg-3 py-1 py-lg-2 navbar-period-badge">
                <i class="bi bi-calendar3 me-1 me-lg-2 text-white"></i>
                <span class="d-none d-md-inline">Periode: </span>
                <span class="text-white fw-bold" style="font-size: 0.75rem;">{{ $activePeriod->tahun_ajaran }} - {{ ucfirst($activePeriod->semester) }}</span>
            </span>
        </div>
        @endif

        {{-- TOGGLER (Kanan) --}}
        <button class="navbar-toggler order-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse order-4 order-lg-2" id="navbarContent">

            <ul class="navbar-nav me-auto">

                <li class="nav-item">
                    @if(Auth::check())
                        @if(Auth::user()->role === 'guru')
                            <a class="nav-link" href="{{ route('dashboard.guru') }}">Dashboard</a>
                        @elseif(in_array(Auth::user()->role, ['kepala_sekolah', 'admin']))
                            <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                        @endif
                    @endif
                </li>

                {{-- MASTER DATA (ADMIN) --}}
                @if(Auth::check() && Auth::user()->role === 'admin')
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        Master Data
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('user.index') }}">User</a></li>
                        <li><a class="dropdown-item" href="{{ route('guru.index') }}">Guru</a></li>
                        <li><a class="dropdown-item" href="{{ route('wali-murid.index') }}">Wali Murid</a></li>
                        <li><a class="dropdown-item" href="{{ route('period.index') }}">Periode</a></li>
                        <li><a class="dropdown-item" href="{{ route('weights.index') }}">Bobot Penilai</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('recommendations.index') }}">Rekomendasi</a></li>
                        <li><a class="dropdown-item" href="{{ route('kpi-questions.index') }}">Pertanyaan KPI</a></li>
                    </ul>
                </li>
                @endif

                {{-- MASTER KPI (KEPALA SEKOLAH) --}}
                @if(Auth::check() && Auth::user()->role === 'kepala_sekolah')
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        Master KPI
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('kpi.index') }}">Indikator KPI</a></li>
                    </ul>
                </li>
                @endif

                {{-- PENILAIAN --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        Penilaian
                    </a>
                    <ul class="dropdown-menu">

                        @if(in_array(Auth::user()->role, ['guru', 'kepala_sekolah', 'wali_murid']))
                        <li>
                            <a class="dropdown-item" href="{{ route('evaluation.index') }}">
                                Form Penilaian
                            </a>
                        </li>
                        @endif

                        @if(in_array(Auth::user()->role, ['admin', 'kepala_sekolah']))
                        <li>
                            <a class="dropdown-item final-score" href="{{ route('finalscore.index') }}">
                                Nilai Akhir
                            </a>
                        </li>
                        @endif

                        @if(in_array(Auth::user()->role, ['admin', 'kepala_sekolah']))
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('riwayat.penilaian') }}">
                                Riwayat Penilaian
                            </a>
                        </li>
                        @endif

                    </ul>
                </li>

            </ul>

            {{-- USER --}}
            <ul class="navbar-nav ms-auto">
                @auth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        Halo, {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item" type="submit">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
                @else
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                </li>
                @endauth
            </ul>

        </div>
    </div>
</nav>
