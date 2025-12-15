<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'periode_id',
        'guru_id',
        'penilai_id',
        'role_penilai',
        'average_score',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function penilai()
    {
        return $this->belongsTo(User::class, 'penilai_id');
    }

    public function period()
    {
        return $this->belongsTo(Period::class, 'periode_id');
    }

    public function details()
    {
        return $this->hasMany(EvaluationDetail::class);
    }
}
