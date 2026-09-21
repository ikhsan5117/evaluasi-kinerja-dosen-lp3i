<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluasi extends Model
{
    use HasFactory;

    protected $table = 'evaluasi';

    protected $fillable = [
        'mahasiswa_id',
        'kelas_mata_kuliah_id',
        'kuesioner_id',
        'tanggal_pengisian',
        'saran_masukan',
        'status',
    ];

    protected $casts = [
        'tanggal_pengisian' => 'date',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function kelasMataKuliah(): BelongsTo
    {
        return $this->belongsTo(KelasMataKuliah::class);
    }

    public function kuesioner(): BelongsTo
    {
        return $this->belongsTo(Kuesioner::class);
    }

    public function jawaban(): HasMany
    {
        return $this->hasMany(JawabanKuesioner::class);
    }

    public function getRataRataAttribute(): float
    {
        return round((float) $this->jawaban()->avg('skor'), 2);
    }
}
