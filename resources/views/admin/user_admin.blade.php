<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard_admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user_admin.css') }}">
</head>
<body>
    <header class="topbar">
        <button id="sidebarToggle" class="hamburger sidebar-toggle" aria-label="Toggle sidebar">☰</button>
        <h1 class="sr-only">User management</h1>
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
                <a href="{{ route('admin.users') }}" class="active">User</a>
                <a href="{{ route('admin.history') }}">History</a>
            </nav>
            <div class="logout">🔒 Logout</div>
        </aside>

        <div class="main-area">
            @if(session('status'))
                <div style="color:green; margin-bottom:10px">{{ session('status') }}</div>
            @endif

            <div class="panel">
                <div class="panel-head" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
                    <h3 style="text-transform: none; font-size:20px;">User management</h3>
                    <a href="#" class="btn" style="display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:8px;background:#fff;color:#7A0200;border:1px solid rgba(122,2,0,0.08);">🔍 Create Account</a>
                </div>

                <div class="user-table-wrapper">
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td class="td-name">{{ $user->name }}</td>
                                    <td class="td-status">{{ $user->role ? 'Active' : 'Inactive' }}</td>
                                    <td class="td-ket">{{ $user->role === 'ra' ? 'Room Attendant' : (ucfirst($user->role ?? '—')) }}</td>
                                    <td class="td-action">
                                        <button class="btn btn-edit" data-id="{{ $user->id }}" data-role="{{ $user->role ?? '' }}" data-name="{{ $user->name }}">Edit</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Role Modal (improved UI) -->
    <div id="editModal" class="modal" aria-hidden="true" style="display:none;">
        <div class="modal-backdrop"></div>
        <div class="modal-panel" role="dialog" aria-modal="true">
            <div class="modal-header">
                <div>
                    <h4 id="modalTitle">Edit role</h4>
                    <div class="modal-sub">Assign a role to the user</div>
                </div>
                <button type="button" class="modal-close" id="modalCancel" aria-label="Close">✕</button>
            </div>

            <form id="roleForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div class="form-row">
                        <label for="roleSelect">Role</label>
                        <select id="roleSelect" name="role">
                            <option value="">-- Pilih role --</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="ra">Room Attendant</option>
                            <option value="manager">Manager</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="modalCancelVisible">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>    

    <footer>
        <div class="footer-inner">&copy; {{ date('Y') }} Hotel Management System. All rights reserved.</div>
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

        // initialize visibility & default mobile state
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

        // Prevent clicks inside sidebar from closing it (allow anchors to navigate)
        sidebar.addEventListener('click', function(e){
            if (e.target.closest('a')) return; // allow link clicks
            e.stopPropagation();
        });

        // Ensure sidebar links always navigate (fallback for SPA/overlay issues)
        document.querySelectorAll('.sidebar nav a').forEach(a => {
            a.addEventListener('click', function(e){
                const href = this.getAttribute('href');
                if (!href || href === '#') return; // ignore placeholders
                e.preventDefault();
                window.location.href = href;
            });
        });

        // logo image fallback handling
        (function(){
            const imgs = sidebar.querySelectorAll('.side-logos img');
            const svgFallback = sidebar.querySelector('.side-logo-svg');
            const anyVisible = Array.from(imgs).some(i => i.complete && i.naturalWidth > 0 && i.style.display !== 'none');
            if (!anyVisible && svgFallback) svgFallback.style.display = 'block';
        })();

        // Edit modal logic (unchanged)
        const editButtons = document.querySelectorAll('.btn-edit');
        const modal = document.getElementById('editModal');
        const roleForm = document.getElementById('roleForm');
        const roleSelect = document.getElementById('roleSelect');
        const modalTitle = document.getElementById('modalTitle');
        const modalCloseButtons = document.querySelectorAll('#modalCancel, #modalCancelVisible, .modal-close');

        function openModal(userId, name, role){
            modal.style.display = 'block';
            modal.setAttribute('aria-hidden','false');
            roleForm.action = '/dashboard-admin/user/' + userId + '/role';
            modalTitle.textContent = 'Edit role — ' + name;
            roleSelect.value = role || 'ra';
            roleSelect.focus();
        }
        function closeModal(){
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden','true');
        }

        editButtons.forEach(btn => btn.addEventListener('click', function(){
            openModal(this.dataset.id, this.dataset.name, this.dataset.role);
        }));

        modalCloseButtons.forEach(b => b.addEventListener('click', closeModal));
        const backdrop = modal.querySelector('.modal-backdrop');
        if (backdrop) backdrop.addEventListener('click', closeModal);
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeModal(); });
    })();
</script>
</body>
</html>
