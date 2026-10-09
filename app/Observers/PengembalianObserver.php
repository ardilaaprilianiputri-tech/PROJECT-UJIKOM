<?php

namespace App\Observers;

use App\Models\Pengembalian;
use App\Models\LogAktivitas;

class PengembalianObserver
{
    /**
     * Handle the Pengembalian "created" event.
     */
    public function created(Pengembalian $pengembalian): void
    {
        LogAktivitas::create([
            'user_id' => auth()->id() ?? $pengembalian->petugas_id,
            'aktivitas' => "Memproses pengembalian alat untuk Peminjaman ID: {$pengembalian->peminjaman_id} dengan kondisi '{$pengembalian->kondisi_kembali}' dan denda Rp " . number_format($pengembalian->denda, 0, ',', '.') . ".",
        ]);
    }

    /**
     * Handle the Pengembalian "updated" event.
     */
    public function updated(Pengembalian $pengembalian): void
    {
        $changes = [];
        foreach ($pengembalian->getChanges() as $key => $newValue) {
            if ($key !== 'updated_at') {
                $oldValue = $pengembalian->getOriginal($key);
                $changes[] = "kolom '{$key}' dari '{$oldValue}' ke '{$newValue}'";
            }
        }

        $detailPerubahan = !empty($changes) ? implode(', ', $changes) : 'memperbarui data';

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => "Memperbarui pengembalian ID {$pengembalian->id}: {$detailPerubahan}.",
        ]);
    }

    /**
     * Handle the Pengembalian "deleted" event.
     */
    public function deleted(Pengembalian $pengembalian): void
    {
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => "Menghapus data pengembalian (ID: {$pengembalian->id}).",
        ]);
    }
}