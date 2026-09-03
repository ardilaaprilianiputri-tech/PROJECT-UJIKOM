<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara eksplisit agar Laravel tidak mencari 'kategoris'
    protected $table = 'kategori'; 

    protected $guarded = [];
}