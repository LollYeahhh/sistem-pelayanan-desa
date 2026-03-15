<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [ 
        'nama_lengkap',
        'nik',
        'email',
        'password',
        'role',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'status_perkawinan',
        'agama',
        'pekerjaan',
        'alamat',
        'rt_rw',
        'desa',
        'kecamatan',
        'no_hp',
        'foto',
        'status_akun',
        'is_verified'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'is_verified' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    public function isRT(): bool
    {
        return $this->role === 'rt';
    }
    public function isWarga(): bool
    {
        return $this->role === 'warga';
    }
}
