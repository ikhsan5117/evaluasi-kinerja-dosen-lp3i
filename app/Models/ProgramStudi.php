<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramStudi extends Model
{
    use HasFactory;

    protected $table = 'program_studi';

    protected $fillable = [
        'kode_prodi',
        'nama_prodi',
        'jenjang',
    ];

    public function mataKuliah(): HasMany
    {
        return $this->hasMany(MataKuliah::class);
    }

    public function dosen(): HasMany
    {
        return $this->hasMany(Dosen::class);
    }

    public function mahasiswa(): HasMany
    {
        return $this->hasMany(Mahasiswa::class);
    }
}
