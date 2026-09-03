@extends('layouts.app')

@section('title', 'Dashboard Admin - Sistem Peminjaman')
@section('header-title', 'Manajemen Pengguna Sistem')

@section('content')
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <div class="p-5 border-b border-gray-200 bg-gray-50/50">
            <h3 class="text-base font-bold text-gray-800">Daftar Pengguna Sistem</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/30 text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <th class="py-3 px-5">Nama</th>
                        <th class="py-3 px-5">Email</th>
                        <th class="py-3 px-5">Role / Hak Akses</th>
                        <th class="py-3 px-5">No. HP</th>
                        <th class="py-3 px-5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse($users ?? [] as $user)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-3.5 px-5 font-semibold text-gray-900">{{ $user->name }}</td>
                            <td class="py-3.5 px-5 text-gray-500">{{ $user->email }}</td>
                            <td class="py-3.5 px-5">
                                @if(strtolower($user->role) == 'admin')
                                    <span class="bg-purple-100 text-purple-700 text-xs font-semibold px-2.5 py-1 rounded-full">Admin</span>
                                @elseif(strtolower($user->role) == 'petugas')
                                    <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-full">Petugas</span>
                                @else
                                    <span class="bg-emerald-100 text-emerald-700 text-xs font-semibold px-2.5 py-1 rounded-full">Peminjam</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-gray-500">{{ $user->no_hp ?? '-' }}</td>
                            <td class="py-3.5 px-5">
                                <a href="#" class="text-xs font-medium text-slate-400 hover:text-slate-600 transition">
                                    Kelola Data
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-3.5 px-5 font-semibold text-gray-900">Administrator</td>
                            <td class="py-3.5 px-5 text-gray-500">admin@app.com</td>
                            <td class="py-3.5 px-5">
                                <span class="bg-purple-100 text-purple-700 text-xs font-semibold px-2.5 py-0.5 rounded-full">Admin</span>
                            </td>
                            <td class="py-3.5 px-5 text-gray-500">-</td>
                            <td class="py-3.5 px-5">
                                <a href="#" class="text-xs font-medium text-slate-400 hover:text-slate-600 transition">Kelola Data</a>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-3.5 px-5 font-semibold text-gray-900">Petugas Lab</td>
                            <td class="py-3.5 px-5 text-gray-500">petugas@app.com</td>
                            <td class="py-3.5 px-5">
                                <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2.5 py-0.5 rounded-full">Petugas</span>
                            </td>
                            <td class="py-3.5 px-5 text-gray-500">-</td>
                            <td class="py-3.5 px-5">
                                <a href="#" class="text-xs font-medium text-slate-400 hover:text-slate-600 transition">Kelola Data</a>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-3.5 px-5 font-semibold text-gray-900">Siswa Peminjam</td>
                            <td class="py-3.5 px-5 text-gray-500">peminjam@app.com</td>
                            <td class="py-3.5 px-5">
                                <span class="bg-emerald-100 text-emerald-700 text-xs font-semibold px-2.5 py-0.5 rounded-full">Peminjam</span>
                            </td>
                            <td class="py-3.5 px-5 text-gray-500">-</td>
                            <td class="py-3.5 px-5">
                                <a href="#" class="text-xs font-medium text-slate-400 hover:text-slate-600 transition">Kelola Data</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection