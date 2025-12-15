<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    protected $fillable = [
        'tahun_ajaran',
        'semester',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    /**
     * Relasi: satu periode memiliki banyak final scores (nilai akhir guru)
     */
    public function finalScores()
    {
        return $this->hasMany(FinalScore::class, 'periode_id');
    }

    /**
     * Relasi: satu periode memiliki banyak evaluations (penilaian per guru)
     */
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'periode_id');
    }

    /**
     * Scope untuk ambil periode aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
