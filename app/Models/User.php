<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [

        'id_pasien_simrs',
        'no_rkm_medis',
        'no_ktp',
        'name',
        'jk',
        'tmp_lahir',
        'tgl_lahir',
        'nm_ibu',
        'no_tlp',
        'alamat',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'tgl_lahir' => 'date:Y-m-d',
            'password' => 'hashed',
        ];
    }
}
