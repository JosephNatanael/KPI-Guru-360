<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EvaluationDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'kpi_question_id',
        'nilai',
    ];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function question()
    {
        return $this->belongsTo(KpiQuestion::class, 'kpi_question_id');
    }
}
