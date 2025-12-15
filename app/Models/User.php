<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'guru_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relasi User → Guru
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    // User memberi penilaian (Evaluations)
    public function evaluationsGiven()
    {
        return $this->hasMany(Evaluation::class, 'penilai_id');
    }
}
