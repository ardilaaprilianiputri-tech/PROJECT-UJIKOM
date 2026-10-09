<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class PengembalianController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();
        $query = Pengembalian::with(['peminjaman.user', 'peminjaman.detailPinjams.alat', 'petugas']);

        if ($user->role === 'peminjaman') {
            $query->whereHas('peminjaman', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $pengembalian = $query->latest()->get();

        return response()->json([
            'message' => 'Riwayat pengembalian berhasil diambil.',
            'data' => $pengembalian
        ]);
    }

    public function show(Pengembalian $pengembalian): JsonResponse
    {
        $user = auth()->user();

        // Eager load relasi
        $pengembalian->load(['peminjaman.user', 'peminjaman.detailPinjams.alat', 'petugas']);

        // Otorisasi privasi
        if ($user->role === 'peminjaman' && $pengembalian->peminjaman->user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return response()->json([
            'message' => 'Detail pengembalian berhasil diambil.',
            'data' => $pengembalian
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'peminjaman_id'   => ['required', 'integer', 'exists:peminjaman,id'],
            'kondisi_kembali' => ['required', 'string', 'max:255'],
            'denda'           => ['nullable', 'integer', 'min:0'],
        ]);

        try {
            $pengembalian = DB::transaction(function () use ($request) {
                // Kunci baris peminjaman ini selama transaksi menggunakan detailPinjams
                $peminjaman = Peminjaman::with('detailPinjams')->lockForUpdate()->find($request->peminjaman_id);

                // Guarding: Pastikan statusnya sedang dipinjam
                if ($peminjaman->status !== 'dipinjam') {
                    throw new Exception("Data ditolak. Peminjaman ini berstatus '{$peminjaman->status}', bukan 'dipinjam'.");
                }

                // Cek keterlambatan menggunakan Carbon
                $tglKembaliPlan = Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
                $hariIni = Carbon::now()->startOfDay();

                $statusPeminjamanBaru = $hariIni->greaterThan($tglKembaliPlan) ? 'telat' : 'dikembalikan';

                // 1. Insert data ke tabel pengembalian
                $pengembalian = Pengembalian::create([
                    'peminjaman_id' => $peminjaman->id,
                    'tgl_kembali' => now()->toDateString(),
                    'kondisi_kembali' => $request->kondisi_kembali,
                    'denda' => $request->denda ?? 0,
                    'petugas_id' => auth()->id(),
                ]);

                // 2. Ubah status di tabel peminjaman utama
                $peminjaman->update(['status' => $statusPeminjamanBaru]);

                // 3. Kembalikan (tambah) stok alat berdasarkan detailPinjams
                foreach ($peminjaman->detailPinjams as $detail) {
                    $alat = Alat::lockForUpdate()->find($detail->alat_id);
                    $alat->increment('stok', $detail->jumlah);
                }

                if (method_exists(auth()->user(), 'logAktivitas')) {
                    auth()->user()->logAktivitas()->create([
                        'aktivitas' => "Memproses pengembalian peminjaman ID: #{$peminjaman->id} dengan status akhir: {$statusPeminjamanBaru}."
                    ]);
                }

                return $pengembalian->load(['peminjaman.user', 'petugas']);
            });

            return response()->json([
                'message' => 'Proses pengembalian alat berhasil diselesaikan.',
                'data' => $pengembalian
            ], 201);

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function update(Request $request, Pengembalian $pengembalian): JsonResponse
    {
        $request->validate([
            'kondisi_kembali' => ['nullable', 'string', 'max:255'],
            'denda'           => ['nullable', 'integer', 'min:0'],
        ]);

        $pengembalian->update([
            'kondisi_kembali' => $request->kondisi_kembali ?? $pengembalian->kondisi_kembali,
            'denda' => $request->denda ?? $pengembalian->denda,
        ]);

        return response()->json([
            'message' => 'Data pengembalian berhasil diperbarui.',
            'data' => $pengembalian->load(['peminjaman.user', 'petugas'])
        ]);
    }

    public function destroy(Pengembalian $pengembalian): JsonResponse
    {
        try {
            DB::transaction(function () use ($pengembalian) {
                $peminjaman = Peminjaman::with('detailPinjams')->lockForUpdate()->findOrFail($pengembalian->peminjaman_id);

                foreach ($peminjaman->detailPinjams as $detail) {
                    $alat = Alat::lockForUpdate()->findOrFail($detail->alat_id);

                    if ($alat->stok < $detail->jumlah) {
                        throw new Exception("Gagal membatalkan pengembalian. Stok alat '{$alat->nama_alat}' saat ini tidak mencukupi untuk ditarik kembali.");
                    }

                    $alat->decrement('stok', $detail->jumlah);
                }

                $peminjaman->update(['status' => 'dipinjam']);

                if (method_exists(auth()->user(), 'logAktivitas')) {
                    auth()->user()->logAktivitas()->create([
                        'aktivitas' => "Membatalkan pengembalian ID: #{$pengembalian->id}"
                    ]);
                }

                $pengembalian->delete();
            });

            return response()->json([
                'message' => 'Data pengembalian berhasil dihapus. Stok dan status peminjaman telah dikembalikan ke kondisi semula.'
            ]);

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}