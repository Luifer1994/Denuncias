<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
/* use Laravel\Sanctum\HasApiTokens; */
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];
    protected $hidden = [
        'password',
        'id_rol',
        'id_profession',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id');
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class, 'id_profession', 'id');
    }

    public function complaint()
    {
        return $this->hasMany(Complaint::class, 'id_user');
    }
}
