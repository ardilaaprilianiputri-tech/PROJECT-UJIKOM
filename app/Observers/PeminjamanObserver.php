<?php

namespace App\Observers;

use App\Models\Peminjaman;
use App\Models\LogAktivitas;

class PeminjamanObserver
{
    /**
     * Handle the Peminjaman "created" event.
     */
    public function created(Peminjaman $peminjaman): void
    {
        LogAktivitas::create([
            'user_id' => auth()->id() ?? $peminjaman->user_id ?? 1,
            'aktivitas' => "Menambahkan data peminjaman baru (ID: {$peminjaman->id}) dengan status: {$peminjaman->status}.",
        ]);
    }

    /**
     * Handle the Peminjaman "updated" event.
     */
    public function updated(Peminjaman $peminjaman): void
    {
        $changes = [];
        foreach ($peminjaman->getChanges() as $key => $newValue) {
            if ($key !== 'updated_at') {
                $oldValue = $peminjaman->getOriginal($key);
                $changes[] = "kolom '{$key}' berubah dari '{$oldValue}' menjadi '{$newValue}'";
            }
        }

        $detailPerubahan = !empty($changes) ? implode(', ', $changes) : 'memperbarui data';

        LogAktivitas::create([
            'user_id' => auth()->id() ?? $peminjaman->user_id ?? 1,
            'aktivitas' => "Memperbarui peminjaman ID {$peminjaman->id}: {$detailPerubahan}.",
        ]);
    }

    /**
     * Handle the Peminjaman "deleted" event.
     */
    public function deleted(Peminjaman $peminjaman): void
    {
        LogAktivitas::create([
            'user_id' => auth()->id() ?? $peminjaman->user_id ?? 1,
            'aktivitas' => "Menghapus data peminjaman (ID: {$peminjaman->id}).",
        ]);
    }
}