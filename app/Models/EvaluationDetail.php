<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EvaluationDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'kpi_indicator_id',
        'nilai',
    ];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function indicator()
    {
        return $this->belongsTo(KpiIndicator::class, 'kpi_indicator_id');
    }

    public function kpi()
    {
        return $this->belongsTo(KpiIndicator::class, 'kpi_indicator_id');
    }
}
