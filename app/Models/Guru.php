<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guru extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'nip',
        'jabatan',
        'is_wali_kelas',
        'kelas',
        'mata_pelajaran',
    ];

    // Relasi Guru → User
    public function user()
    {
        return $this->hasOne(User::class, 'guru_id');
    }

    // Penilaian untuk guru
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    // Nilai akhir guru
    public function finalScores()
    {
        return $this->hasMany(FinalScore::class);
    }
}
