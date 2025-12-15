<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KpiIndicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'kompetensi',
        'bobot',
    ];

    public function details()
    {
        return $this->hasMany(EvaluationDetail::class);
    }
}
