<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kuesioner extends Model
{
    use HasFactory;

    protected $table = 'kuesioner';

    protected $fillable = [
        'judul',
        'deskripsi',
        'kategori',
        'periode_id',
        'status',
        'created_by',
    ];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pertanyaan(): HasMany
    {
        return $this->hasMany(Pertanyaan::class)->orderBy('nomor_urut');
    }

    public function evaluasi(): HasMany
    {
        return $this->hasMany(Evaluasi::class);
    }
}
