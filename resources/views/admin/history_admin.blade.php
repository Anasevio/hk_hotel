<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History - Admin</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard_admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/history_admin.css') }}">
</head>
<body>
    <header class="topbar">
        <button id="sidebarToggle" class="hamburger sidebar-toggle" aria-label="Toggle sidebar">☰</button>
        <h1 class="sr-only">History</h1>
    </header>

    <!-- fixed open button (visible when sidebar is hidden) -->
    <button id="sidebarOpenFixed" class="hamburger fixed-toggle" aria-label="Open sidebar" title="Open sidebar">
        <img src="{{ asset('images/pasted-image-2.png') }}" alt="Open" onerror="this.style.display='none'" />
        <span class="fixed-fallback">☰</span>
    </button>

    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <div class="sidebar-top">
                <button class="hamburger small sidebar-toggle" aria-label="Toggle sidebar (inline)">☰</button>
                <div class="side-logos">
                    <img src="{{ asset('images/OIP-removebg-preview.png') }}" alt="Logo 1" onerror="this.style.display='none'">
                    <img src="{{ asset('images/Whatsapp.png') }}" alt="Logo 2" onerror="this.style.display='none'">
                    <div class="side-logo-svg"><x-application-logo class="side-svg" /></div>
                </div>
            </div>

            <div class="logo">
                <div><strong>SMK SIG</strong></div>
                <div style="font-size:12px;opacity:0.9">Hotel Admin</div>
            </div>

            <nav>
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a href="{{ route('admin.users') }}">User</a>
                <a href="{{ route('admin.history') }}" class="active">History</a>
            </nav>
            <div class="logout">🔒 Logout</div>
        </aside>

        <div class="main-area">
            <div class="page-inner page-inner--flush-left">
                <div class="panel" style="padding:22px 22px 30px;">
                    <div class="panel-head" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
                        <h3 style="text-transform: none; font-size:28px;">History</h3>
                    </div>

                <div class="history-table-wrapper">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kamar</th>
                                <th class="text-center">Checklist</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php /** @var \Illuminate\Database\Eloquent\Model $item */ @endphp
                            @forelse($histories ?? [] as $item)
                                <tr>
                                    <td class="td-date">{{ \Carbon\Carbon::parse($item->checked_at)->format('j M. Y') }}</td>
                                    <td class="td-room">{{ $item->room_name }}</td>
                                    <td class="td-check text-center">
                                        <span class="check-count">{{ $item->completed_check_count ?? 0 }}</span>
                                    </td>
                                    <td class="td-status">
                                        @if($item->on_time)
                                            <span class="status badge ontime">✔ Selesai Tepat Waktu</span>
                                        @else
                                            <span class="status badge late">✔ Selesai, Tidak Tepat Waktu</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                {{-- Example rows so layout matches design even if controller not yet supplying data --}}
                                @for($i=1;$i<=9;$i++)
                                    <tr>
                                        <td class="td-date">{{ $i <= 6 ? '5 Feb. 2026' : '4 Feb. 2026' }}</td>
                                        <td class="td-room">Kamar {{ sprintf('%02d', $i) }}</td>
                                        <td class="td-check text-center"><span class="check-count">12</span></td>
                                        <td class="td-status"><span class="status badge {{ $i % 2 == 0 ? 'late' : 'ontime' }}">✔ {{ $i % 2 == 0 ? 'Selesai, Tidak Tepat Waktu' : 'Selesai Tepat Waktu' }}</span></td>
                                    </tr>
                                @endfor
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <footer>
        &copy; {{ date('Y') }} Hotel Management System. All rights reserved.
    </footer>

<script>
    (function(){
        const wrapper = document.querySelector('.dashboard-wrapper');
        const sidebar = document.querySelector('.sidebar');
        const toggles = document.querySelectorAll('.sidebar-toggle');
        const fixedToggle = document.getElementById('sidebarOpenFixed');

        function updateFixedToggleVisibility() {
            if (!fixedToggle) return;
            if (wrapper.classList.contains('sidebar-hidden')) {
                fixedToggle.style.display = 'inline-flex';
            } else {
                fixedToggle.style.display = 'none';
            }
        }

        toggles.forEach(btn => btn.addEventListener('click', function(e){
            e.stopPropagation();
            wrapper.classList.toggle('sidebar-hidden');
            updateFixedToggleVisibility();
        }));

        if (fixedToggle) {
            fixedToggle.addEventListener('click', function(e){
                e.stopPropagation();
                wrapper.classList.remove('sidebar-hidden');
                updateFixedToggleVisibility();
            });
        }

        document.addEventListener('click', function(e){
            if (!sidebar.contains(e.target) && !Array.from(toggles).some(t=>t.contains(e.target)) && !(fixedToggle && fixedToggle.contains(e.target))) {
                wrapper.classList.add('sidebar-hidden');
                updateFixedToggleVisibility();
            }
        });

        updateFixedToggleVisibility();

        if (window.innerWidth <= 768) {
            wrapper.classList.add('sidebar-hidden');
            updateFixedToggleVisibility();
        }

        window.addEventListener('resize', function(){
            if (window.innerWidth <= 768) {
                wrapper.classList.add('sidebar-hidden');
            } else {
                wrapper.classList.remove('sidebar-hidden');
            }
            updateFixedToggleVisibility();
        });

        document.addEventListener('keydown', function(e){
            if (e.key === 'Escape' && window.innerWidth <= 768) {
                wrapper.classList.add('sidebar-hidden');
                updateFixedToggleVisibility();
            }
        });

        sidebar.addEventListener('click', function(e){
            if (e.target.closest('a')) return;
            e.stopPropagation();
        });

        document.querySelectorAll('.sidebar nav a').forEach(a => {
            a.addEventListener('click', function(e){
                const href = this.getAttribute('href');
                if (!href || href === '#') return;
                e.preventDefault();
                window.location.href = href;
            });
        });

        (function(){
            const imgs = sidebar.querySelectorAll('.side-logos img');
            const svgFallback = sidebar.querySelector('.side-logo-svg');
            const anyVisible = Array.from(imgs).some(i => i.complete && i.naturalWidth > 0 && i.style.display !== 'none');
            if (!anyVisible && svgFallback) svgFallback.style.display = 'block';
        })();
    })();
</script>
</body>
</html>