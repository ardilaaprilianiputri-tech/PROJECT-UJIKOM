@extends('layouts.app')

@section('title', 'Manajemen User - Panel Admin')
@section('header-title', 'Daftar Pengguna Sistem')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
        <h3 class="text-lg font-bold text-gray-800">Daftar Pengguna Sistem</h3>

        <form action="{{ route('admin.user.index') }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." 
                class="w-full md:w-56 px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500">

            <select name="role" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500">
                <option value="">Semua Role</option>
                <option value="Admin" {{ request('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                <option value="Petugas" {{ request('role') == 'Petugas' ? 'selected' : '' }}>Petugas</option>
                <option value="Peminjam" {{ request('role') == 'Peminjam' ? 'selected' : '' }}>Peminjam</option>
            </select>

            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-3.5 py-1.5 text-sm rounded-lg transition">
                Cari
            </button>

            @if(request('search') || request('role'))
                <a href="{{ route('admin.user.index') }}" class="text-sm text-gray-600 hover:underline whitespace-nowrap ml-1">Reset</a>
            @endif
        </form>

        <a href="{{ route('admin.user.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition whitespace-nowrap">
            + Tambah User
        </a>
    </div>

    {{-- Alert Notifikasi Sukses --}}
    @if(session('success'))
        <div class="m-5 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg text-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="font-bold text-green-700 ml-2">&times;</button>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-700 text-xs uppercase tracking-wider border-b">
                    <th class="py-3 px-4 font-semibold">No</th>
                    <th class="py-3 px-4 font-semibold">Nama</th>
                    <th class="py-3 px-4 font-semibold">Email</th>
                    <th class="py-3 px-4 font-semibold">Role</th>
                    <th class="py-3 px-4 font-semibold">No. HP</th>
                    <th class="py-3 px-4 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                @forelse ($users as $index => $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-4">{{ $users->firstItem() ? $users->firstItem() + $index : $index + 1 }}</td>
                        <td class="py-3 px-4 font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="py-3 px-4">{{ $user->email }}</td>
                        <td class="py-3 px-4">
                            {{-- Menggunakan strtolower agar tidak error karena beda huruf besar/kecil --}}
                            @php $role = strtolower($user->role); @endphp

                            @if($role == 'admin')
                                <span class="bg-purple-100 text-purple-800 text-xs px-2.5 py-0.5 rounded font-semibold inline-block">Admin</span>
                            @elseif($role == 'petugas')
                                <span class="bg-blue-100 text-blue-800 text-xs px-2.5 py-0.5 rounded font-semibold inline-block">Petugas</span>
                            @else
                                <span class="bg-green-100 text-green-800 text-xs px-2.5 py-0.5 rounded font-semibold inline-block">Peminjam</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">{{ $user->no_hp ?? '-' }}</td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('admin.user.edit', $user->id) }}" 
                                   class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                    Edit
                                </a>

                                <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST" 
                                      onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-gray-500">
                            Tidak ada data pengguna yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($users, 'links'))
        <div class="p-4 border-t border-gray-200">
            {{ $users->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection