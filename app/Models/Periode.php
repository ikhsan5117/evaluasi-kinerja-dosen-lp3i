<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Periode extends Model
{
    use HasFactory;

    protected $table = 'periode';

    protected $fillable = [
        'tahun_ajaran',
        'semester',
        'status',
    ];

    public function kelasMataKuliah(): HasMany
    {
        return $this->hasMany(KelasMataKuliah::class);
    }

    public function kuesioner(): HasMany
    {
        return $this->hasMany(Kuesioner::class);
    }

    public function getNamaPeriodeAttribute(): string
    {
        return "{$this->tahun_ajaran} ({$this->semester})";
    }
}
