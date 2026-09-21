<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewRekapEvaluasiKelas extends Model
{
    protected $table = 'view_rekap_evaluasi_kelas';
    protected $primaryKey = 'kelas_mata_kuliah_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'kelas_mata_kuliah_id' => 'integer',
        'dosen_id' => 'integer',
        'mata_kuliah_id' => 'integer',
        'periode_id' => 'integer',
        'total_responden' => 'integer',
        'avg_pedagogik' => 'float',
        'avg_profesional' => 'float',
        'avg_kepribadian' => 'float',
        'avg_sosial' => 'float',
        'avg_total' => 'float',
    ];

    public function kelasMataKuliah()
    {
        return $this->belongsTo(KelasMataKuliah::class, 'kelas_mata_kuliah_id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class, 'periode_id');
    }

    public function getPredikatAttribute(): string
    {
        if ($this->total_responden <= 0 || !$this->avg_total) {
            return 'Belum Ada Penilaian';
        }

        if ($this->avg_total >= 4.5) return 'Sangat Baik';
        if ($this->avg_total >= 3.75) return 'Baik';
        if ($this->avg_total >= 3.0) return 'Cukup';
        return 'Kurang';
    }
}
