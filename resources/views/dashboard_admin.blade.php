<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard_admin.css') }}">
</head>
<body>
    <header class="topbar">
        <button id="sidebarToggle" class="hamburger sidebar-toggle" aria-label="Toggle sidebar">☰</button>
        <h1 class="sr-only">Admin Dashboard</h1>
    </header>

    <!-- fixed open button (visible when sidebar is hidden). shows logo2 when sidebar is closed -->
    <button id="sidebarOpenFixed" class="hamburger fixed-toggle" aria-label="Open sidebar" title="Open sidebar">
        <img src="{{ asset('images/pasted-image-2.png') }}" alt="Open" onerror="this.style.display='none'" />
        <span class="fixed-fallback">☰</span>
    </button>

    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <div class="sidebar-top">
                <button class="hamburger small sidebar-toggle" aria-label="Toggle sidebar (inline)">☰</button>
                <div class="side-logos">
                    <!-- preferred logos (place these files into public/images/) -->
                    <img src="{{ asset('images/OIP-removebg-preview.png') }}" alt="Logo 1" onerror="this.style.display='none'">
                    <img src="{{ asset('images/Whatsapp.png') }}" alt="Logo 2" onerror="this.style.display='none'">

                    <!-- fallback SVG shown when images are missing -->
                    <div class="side-logo-svg"><x-application-logo class="side-svg" /></div>
                </div>
            </div>

            <div class="logo">
                <div><strong>SMK SIG</strong></div>
                <div style="font-size:12px;opacity:0.9">Hotel Admin</div>
            </div>

            <nav>
                <a href="#" class="active">Dashboard</a>
                <a href="{{ route('admin.users') }}">User</a>
                <a href="#">History</a>
            </nav>
            <div class="logout">🔒 Logout</div>
        </aside>

        <div class="main-area">
            @if(session('status'))
                <div style="color:green; margin-bottom:10px">{{ session('status') }}</div>
            @endif

            <div class="stats">
                <div class="card">
                    <div>
                        <div class="label">Total Account</div>
                        <div class="value">63</div>
                    </div>
                    <div>👤</div>
                </div>
                <div class="card center">
                    <div class="label">Hadir Bulan Ini</div>
                    <div class="value">50</div>
                </div>
                <div class="card">
                    <div>
                        <div class="label">Activity log</div>
                        <div class="value">04</div>
                    </div>
                    <div>🕒</div>
                </div>
            </div>

            <div class="panels">
                <div class="panel">
                    <h3>user management</h3>
                    <ul class="user-list">
                        @foreach($users as $user)
                            <li class="user-item">
                                <div class="info">
                                    <div class="name">{{ $user->name }}</div>
                                    <div class="role" style="font-size:12px;color:#666">{{ $user->email }}</div>
                                </div>
                                @if(!empty($dbAvailable) && $dbAvailable)
                                    <form action="{{ route('admin.assignTask', $user->id ?? $user) }}" method="POST">
                                        @csrf
                                        <button class="btn" type="submit">Tugas</button>
                                    </form>
                                @else
                                    <button class="btn" disabled>Tugas (offline)</button>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="panel">
                    <h3>Activity Log</h3>
                    @if($activities->isEmpty())
                        <div class="activity-empty">No activities yet.</div>
                    @else
                        <div class="activity-table-wrapper">
                        <table class="activity-table">
                            <thead>
                                <tr><th>Date</th><th>User</th><th>Room</th><th>Description</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                @foreach($activities as $act)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($act->created_at)->format('d M Y') }}</td>
                                        <td>{{ $act->user?->name ?? 'System' }}</td>
                                        <td>{{ $act->room }}</td>
                                        <td>{{ $act->description }}</td>
                                        <td>{{ $act->status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
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

            // Toggle sidebar visibility from any toggle button
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

            // Fixed open button toggles sidebar when it's hidden
            if (fixedToggle) {
                fixedToggle.addEventListener('click', function(e){
                    e.stopPropagation();
                    wrapper.classList.remove('sidebar-hidden');
                    updateFixedToggleVisibility();
                });
            }

            // Close sidebar when clicking outside (useful on small screens)
            document.addEventListener('click', function(e){
                // if click outside sidebar and not on a toggle, hide it
                if (!sidebar.contains(e.target) && !Array.from(toggles).some(t=>t.contains(e.target)) && (!fixedToggle || !fixedToggle.contains(e.target))) {
                    wrapper.classList.add('sidebar-hidden');
                    updateFixedToggleVisibility();
                }
            });

            // initialize fixed toggle visibility on load
            updateFixedToggleVisibility();

            // hide sidebar by default on small screens for a cleaner mobile-first UX
            if (window.innerWidth <= 768) {
                wrapper.classList.add('sidebar-hidden');
                updateFixedToggleVisibility();
            }

            // keep sidebar state consistent when resizing the viewport
            window.addEventListener('resize', function(){
                if (window.innerWidth <= 768) {
                    wrapper.classList.add('sidebar-hidden');
                } else {
                    wrapper.classList.remove('sidebar-hidden');
                }
                updateFixedToggleVisibility();
            });

            // close sidebar when pressing Escape (mobile accessibility)
            document.addEventListener('keydown', function(e){
                if (e.key === 'Escape' && window.innerWidth <= 768) {
                    wrapper.classList.add('sidebar-hidden');
                    updateFixedToggleVisibility();
                }
            });

            // Prevent clicks inside sidebar from closing it (but allow link navigation)
            sidebar.addEventListener('click', function(e){
                // allow normal behavior for anchors so navigation still works
                if (e.target.closest('a')) return;
                e.stopPropagation();
            });

            // Ensure sidebar <a> links always navigate (fallback for environments that block default)
            document.querySelectorAll('.sidebar nav a').forEach(a => {
                a.addEventListener('click', function(e){
                    // if anchor has a valid href, force navigation (prevents interference from overlays/JS)
                    const href = this.getAttribute('href');
                    if (!href) return;
                    // normal navigation for same-origin href
                    if (href.startsWith('http') || href.startsWith('/')) {
                        e.preventDefault();
                        window.location.href = href;
                    }
                });
            });

            // If logo images are not available, show SVG fallback
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
