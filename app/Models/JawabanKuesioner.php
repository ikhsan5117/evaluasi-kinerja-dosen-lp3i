<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JawabanKuesioner extends Model
{
    use HasFactory;

    protected $table = 'jawaban_kuesioner';

    protected $fillable = [
        'evaluasi_id',
        'pertanyaan_id',
        'skor',
        'catatan',
    ];

    public function evaluasi(): BelongsTo
    {
        return $this->belongsTo(Evaluasi::class);
    }

    public function pertanyaan(): BelongsTo
    {
        return $this->belongsTo(Pertanyaan::class);
    }
}
