<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinalScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'guru_id',
        'periode_id',
        'nilai_kepala_sekolah',
        'nilai_rekan_guru',
        'nilai_wali_murid',
        'nilai_akhir',
        'recommendation_id',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class, 'periode_id');
    }

    public function recommendation()
    {
        return $this->belongsTo(Recommendation::class);
    }
}
