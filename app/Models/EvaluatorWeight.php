<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluatorWeight extends Model
{
    protected $fillable = [
        'jenis_guru',
        'kepala_sekolah',
        'rekan_guru',
        'wali_murid'
    ];

    protected $casts = [
        'kepala_sekolah' => 'integer',
        'rekan_guru' => 'integer',
        'wali_murid' => 'integer'
    ];
}
