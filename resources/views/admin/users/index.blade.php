@extends('layouts.app')

@section('title', 'Pengawasan Akun - Admin')

@section('content')
<a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm" style="margin-bottom: 1.5rem;">← Kembali ke Dashboard Admin</a>

<div class="admin-page-header">
    <div><div style="font-size:.875rem; color:var(--text-muted); margin-bottom:.25rem;">Panel Administrasi</div><h1 style="font-size:2rem;">Pengelolaan Akun</h1><p style="color:var(--text-secondary); margin-top:.25rem;">Kelola role dan status akun pengguna secara aman.</p></div>
</div>

<div style="display:flex; gap:.75rem; margin-bottom:1.5rem; flex-wrap:wrap;">
    @foreach(['all'=>'Semua', 'user'=>'Pembaca', 'kreator'=>'Kreator', 'admin'=>'Admin'] as $key => $label)
        <a class="btn btn-sm {{ $role === $key ? 'btn-primary' : 'btn-secondary' }}" href="{{ route('admin.users.index', ['role'=>$key]) }}">{{ $label }} ({{ $counts[$key] }})</a>
    @endforeach
</div>

<div class="card admin-table-card">
    <div class="admin-table-title"><h2 style="font-size:1.15rem;">Daftar Akun</h2><span style="font-size:.85rem; color:var(--text-muted);">{{ $users->total() }} akun</span></div>
    <div style="overflow-x:auto;"><table class="table"><thead><tr><th>Akun</th><th>Role</th><th>Status</th><th>Aktivitas</th><th>Aksi</th></tr></thead><tbody>
        @forelse($users as $user)
            <tr>
                <td><div style="font-weight:700; color:#fff;">{{ $user->name }}</div><div style="font-size:.8rem; color:var(--text-muted);">{{ $user->email }}</div></td>
                <td><form id="account-{{ $user->id }}" method="POST" action="{{ route('admin.users.update', $user) }}" onsubmit="return confirm('Simpan perubahan role atau status untuk akun ini?');">@csrf @method('PATCH')<select name="role" class="form-control" style="min-width:130px; padding:.5rem .7rem; font-size:.8rem;">@foreach(['user'=>'Pembaca','kreator'=>'Kreator','admin'=>'Admin'] as $value=>$label)<option value="{{ $value }}" @selected($user->role === $value)>{{ $label }}</option>@endforeach</select></form></td>
                <td><select name="is_active" class="form-control" style="min-width:130px; padding:.5rem .7rem; font-size:.8rem;" form="account-{{ $user->id }}"><option value="1" @selected($user->is_active)>Aktif</option><option value="0" @selected(! $user->is_active)>Nonaktif</option></select></td>
                <td style="font-size:.85rem; color:var(--text-secondary); white-space:nowrap;">@if($user->isKreator()) {{ $user->comics_count }} komik · {{ $user->comments_count }} komentar @elseif($user->isAdmin()) Akun Administrator @else {{ $user->comments_count ? $user->comments_count.' komentar' : 'Belum ada aktivitas' }} @endif</td>
                <td style="white-space:nowrap;"><div style="display:inline-flex; gap:.5rem;"><button class="btn btn-secondary btn-sm" form="account-{{ $user->id }}">Simpan</button><a class="btn btn-secondary btn-sm" href="{{ route('admin.users.show', $user) }}">Detail</a></div></td>
            </tr>
        @empty
            <tr><td colspan="5" style="text-align:center; color:var(--text-muted); padding:3rem 1.25rem;">Tidak ada akun pada kategori ini.</td></tr>
        @endforelse
    </tbody></table></div>
    <div style="padding:1rem 1.5rem; border-top:1px solid var(--border-color);">{{ $users->links() }}</div>
</div>
@endsection
