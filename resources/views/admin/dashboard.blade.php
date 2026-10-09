@extends('layouts.app')

@section('title', 'Dashboard Admin - Sistem Peminjaman')
@section('header-title', 'Ringkasan Aktivitas Sistem')

@section('content')
    <!-- Notifikasi Selamat Datang -->
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 text-sm font-medium">
        Selamat datang, **{{ auth()->user()->name ?? 'Administrator' }}**! Anda login sebagai hak akses **{{ auth()->user()->role ?? 'ADMIN' }}**.
    </div>

    <!-- Kotak Log Aktivitas Terbaru -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <div class="p-5 border-b border-gray-200 bg-gray-50/50">
            <h3 class="text-base font-bold text-gray-800">Log Aktivitas Terbaru</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/30 text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <th class="py-3 px-5">WAKTU</th>
                        <th class="py-3 px-5">USER</th>
                        <th class="py-3 px-5">AKTIVITAS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse($logs ?? [] as $log)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-3.5 px-5 text-gray-500 whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="py-3.5 px-5 font-semibold text-gray-900">{{ $log->user->name ?? 'Sistem' }}</td>
                            <td class="py-3.5 px-5 text-gray-700">{{ $log->aktivitas }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-gray-500">Belum ada log aktivitas yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection