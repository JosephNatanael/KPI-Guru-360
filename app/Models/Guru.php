<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guru extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama',
        'is_wali_kelas',
        'kelas',
        'user_id',
    ];

    // Relasi Guru → User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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
