<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosen';

    protected $fillable = [
        'user_id',
        'nidn',
        'gelar',
        'program_studi_id',
        'jabatan_fungsional',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function kelasMataKuliah(): HasMany
    {
        return $this->hasMany(KelasMataKuliah::class);
    }

    public function getNamaLengkapAttribute(): string
    {
        $nama = $this->user ? $this->user->name : '-';
        return $this->gelar ? "{$nama}, {$this->gelar}" : $nama;
    }
}
