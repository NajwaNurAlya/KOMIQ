<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $role = $request->query('role', 'all');
        if (! in_array($role, ['all', 'user', 'kreator', 'admin'], true)) $role = 'all';

        $query = User::withCount(['comics', 'comments'])->latest();
        if ($role !== 'all') $query->where('role', $role);

        return view('admin.users.index', [
            'users' => $query->paginate(20)->withQueryString(),
            'role' => $role,
            'counts' => [
                'all' => User::count(),
                'user' => User::where('role', 'user')->count(),
                'kreator' => User::where('role', 'kreator')->count(),
                'admin' => User::where('role', 'admin')->count(),
            ],
        ]);
    }

    public function show(User $user): View
    {
        $user->load(['comics.chapters', 'comments.chapter.comic']);
        return view('admin.users.show', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:user,kreator,admin'],
            'is_active' => ['required', 'boolean'],
        ]);

        if ($user->is($request->user()) && ($validated['role'] !== 'admin' || ! $validated['is_active'])) {
            return back()->with('error', 'Admin tidak dapat mengubah role atau menonaktifkan akun sendiri.');
        }

        $removesActiveAdmin = $user->isAdmin() && ($validated['role'] !== 'admin' || ! $validated['is_active']);
        if ($removesActiveAdmin && User::where('role', 'admin')->where('is_active', true)->count() <= 1) {
            return back()->with('error', 'Minimal harus ada satu akun Admin aktif.');
        }

        $changes = ['role' => $validated['role'], 'is_active' => (bool) $validated['is_active']];
        if ($user->role === $changes['role'] && $user->is_active === $changes['is_active']) {
            return back()->with('warning', 'Tidak ada perubahan pada role atau status akun.');
        }

        $roleChanged = $user->role !== $changes['role'];
        $statusChanged = $user->is_active !== $changes['is_active'];
        $user->update($changes);

        return back()->with('success', match (true) {
            $roleChanged && $statusChanged => 'Role dan status akun berhasil diperbarui.',
            $roleChanged => 'Role akun berhasil diperbarui.',
            default => 'Status akun berhasil diperbarui.',
        });
    }
}
