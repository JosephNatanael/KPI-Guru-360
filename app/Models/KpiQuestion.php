<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KpiQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'kpi_indicator_id',
        'periode_id',
        'role_penilai',
        'pertanyaan',
        'urutan',
    ];

    public function kpi()
    {
        return $this->belongsTo(KpiIndicator::class, 'kpi_indicator_id');
    }

    public function period()
    {
        return $this->belongsTo(Period::class, 'periode_id');
    }
}
