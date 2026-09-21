<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KelasMataKuliah extends Model
{
    use HasFactory;

    protected $table = 'kelas_mata_kuliah';

    protected $fillable = [
        'mata_kuliah_id',
        'dosen_id',
        'periode_id',
        'nama_kelas',
        'ruangan',
        'jadwal',
    ];

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class);
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function evaluasi(): HasMany
    {
        return $this->hasMany(Evaluasi::class);
    }
}
