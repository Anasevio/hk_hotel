<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users', ['users' => User::orderBy('role')->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $d = $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,supervisor,manager,ra',
        ]);
        User::create([...$d, 'password' => Hash::make($d['password']), 'is_active' => true]);
        return back()->with('success', "Akun {$d['name']} berhasil dibuat.");
    }

    public function update(Request $request, User $user)
    {
        $d = $request->validate([
            'name'     => 'required|string|max:100',
            'username' => ['required','string','max:50', Rule::unique('users')->ignore($user->id)],
            'role'     => 'required|in:admin,supervisor,manager,ra',
            'password' => 'nullable|string|min:6',
        ]);
        $update = ['name' => $d['name'], 'username' => $d['username'], 'role' => $d['role']];
        if (!empty($d['password'])) $update['password'] = Hash::make($d['password']);
        $user->update($update);
        return back()->with('success', "Akun {$user->name} diupdate.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) return back()->with('error', 'Tidak bisa hapus akun sendiri.');
        $name = $user->name;
        $user->delete();
        return back()->with('success', "Akun {$name} dihapus.");
    }

    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id()) return back()->with('error', 'Tidak bisa nonaktifkan akun sendiri.');
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun {$user->name} {$status}.");
    }
}
