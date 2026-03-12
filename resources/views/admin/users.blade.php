@extends('layouts.admin')
@section('title','Kelola Akun')
@section('page-title','Kelola Akun')
@section('sidebar-nav')
<a href="{{ route('admin.dashboard') }}"><i data-feather="grid"></i> Dashboard</a>
<a href="{{ route('admin.rooms.index') }}"><i data-feather="home"></i> Status Kamar</a>
<a href="{{ route('admin.users.index') }}" class="active"><i data-feather="users"></i> Kelola Akun</a>
<a href="{{ route('admin.attendance.index') }}"><i data-feather="calendar"></i> Absensi</a>
<a href="{{ route('admin.timer.index') }}"><i data-feather="clock"></i> Timer Tugas</a>
<a href="{{ route('admin.history.index') }}"><i data-feather="activity"></i> Log Aktivitas</a>
@endsection
@if($errors->any())
<div class="alert alert-error">
    <ul style="margin:0;padding-left:16px">
        @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif
@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title">Daftar Akun ({{ $users->count() }})</span>
        <button class="btn-sm btn-primary" onclick="document.getElementById('modalTambah').classList.add('open')">+ Tambah Akun</button>
    </div>
    <div style="overflow-x:auto">
    <table class="tbl">
        <thead><tr>
            <th>Nama</th><th>Username</th><th>Role</th><th>Shift</th><th>Status</th><th>Aksi</th>
        </tr></thead>
        <tbody>
        @foreach($users as $u)
        <tr>
            <td><strong>{{ $u->name }}</strong></td>
            <td><code style="background:#f5f5f5;padding:2px 6px;border-radius:4px">{{ $u->username }}</code></td>
            <td><span class="badge badge-red">{{ ucfirst($u->role) }}</span></td>
            <td>{{ ucfirst($u->shift) }}</td>
            <td><span class="badge {{ $u->is_active ? 'badge-green' : 'badge-gray' }}">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
            <td style="display:flex;gap:6px;flex-wrap:wrap">
                <button class="btn-sm btn-secondary"
                    onclick="editUser({{ $u->id }},'{{ addslashes($u->name) }}','{{ $u->username }}','{{ $u->role }}','{{ $u->shift }}')">Edit</button>
                @if($u->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.toggle', $u) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-sm {{ $u->is_active ? 'btn-danger' : 'btn-success' }}">
                        {{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                    onsubmit="return confirm('Hapus akun {{ $u->name }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-sm btn-danger">Hapus</button>
                </form>
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal-bg" id="modalTambah">
    <div class="modal">
        <div class="modal-title">➕ Tambah Akun Baru</div>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="form-group"><label class="form-label">Nama Lengkap</label>
                <input name="name" class="form-control" required></div>
            <div class="grid-2">
                <div class="form-group"><label class="form-label">Username</label>
                    <input name="username" class="form-control" required></div>
                <div class="form-group"><label class="form-label">Password</label>
                    <input name="password" type="password" class="form-control" required></div>
            </div>
            <div class="grid-2">
                <div class="form-group"><label class="form-label">Role</label>
                    <select name="role" class="form-control">
                        <option value="ra">Room Attendant</option>
                        <option value="supervisor">Supervisor</option>
                        <option value="manager">Manager</option>
                        <option value="admin">Admin</option>
                    </select></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-sm btn-secondary" onclick="document.getElementById('modalTambah').classList.remove('open')">Batal</button>
                <button type="submit" class="btn-sm btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal-bg" id="modalEdit">
    <div class="modal">
        <div class="modal-title">✏️ Edit Akun</div>
        <form method="POST" id="formEdit">
            @csrf @method('PUT')
            <div class="form-group"><label class="form-label">Nama Lengkap</label>
                <input name="name" id="editName" class="form-control" required></div>
            <div class="grid-2">
                <div class="form-group"><label class="form-label">Username</label>
                    <input name="username" id="editUsername" class="form-control" required></div>
                <div class="form-group"><label class="form-label">Password Baru (opsional)</label>
                    <input name="password" type="password" class="form-control" placeholder="Kosongkan jika tidak diubah"></div>
            </div>
            <div class="grid-2">
                <div class="form-group"><label class="form-label">Role</label>
                    <select name="role" id="editRole" class="form-control">
                        <option value="ra">Room Attendant</option>
                        <option value="supervisor">Supervisor</option>
                        <option value="manager">Manager</option>
                        <option value="admin">Admin</option>
                    </select></div>
                <div class="form-group"><label class="form-label">Shift</label>
                    <select name="shift" id="editShift" class="form-control">
                        <option value="pagi">Pagi</option>
                        <option value="siang">Siang</option>
                        <option value="malam">Malam</option>
                    </select></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-sm btn-secondary" onclick="document.getElementById('modalEdit').classList.remove('open')">Batal</button>
                <button type="submit" class="btn-sm btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editUser(id, name, username, role, shift) {
    document.getElementById('formEdit').action = '/admin/users/' + id;
    document.getElementById('editName').value = name;
    document.getElementById('editUsername').value = username;
    document.getElementById('editRole').value = role;
    document.getElementById('editShift').value = shift;
    document.getElementById('modalEdit').classList.add('open');
}
document.querySelectorAll('.modal-bg').forEach(m => {
    m.addEventListener('click', e => { if(e.target === m) m.classList.remove('open'); });
});
</script>
@endpush
