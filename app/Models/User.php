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
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function waliMurid()
    {
        return $this->hasOne(WaliMurid::class);
    }

    // Relasi User → Guru (One to One)
    // User sekarang memiliki 1 profil Guru (User::hasOne::Guru)
    public function guru()
    {
        return $this->hasOne(Guru::class, 'user_id');
    }

    // User memberi penilaian (Evaluations)
    public function evaluationsGiven()
    {
        return $this->hasMany(Evaluation::class, 'penilai_id');
    }
}
