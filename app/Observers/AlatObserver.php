<?php

namespace App\Observers;

use App\Models\Alat;
use App\Models\LogAktivitas;

class AlatObserver
{
    /**
     * Handle the Alat "created" event.
     */
    public function created(Alat $alat): void
    {
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => "Menambahkan data alat baru: '{$alat->nama_alat}' (Stok: {$alat->stok}, Kondisi: {$alat->status_kondisi}).",
        ]);
    }

    /**
     * Handle the Alat "updated" event.
     */
    public function updated(Alat $alat): void
    {
        $changes = [];
        foreach ($alat->getChanges() as $key => $newValue) {
            if ($key !== 'updated_at') {
                $oldValue = $alat->getOriginal($key);
                $changes[] = "kolom '{$key}' berubah dari '{$oldValue}' menjadi '{$newValue}'";
            }
        }

        $detailPerubahan = !empty($changes) ? implode(', ', $changes) : 'memperbarui data alat';

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => "Memperbarui alat '{$alat->nama_alat}': {$detailPerubahan}.",
        ]);
    }

    /**
     * Handle the Alat "deleted" event.
     */
    public function deleted(Alat $alat): void
    {
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => "Menghapus data alat: '{$alat->nama_alat}'.",
        ]);
    }
}