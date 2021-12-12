<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    use HasFactory;
    protected $hidden = [
        'created_at',
        'updated_at',
        'state'
    ];

    public function user()
    {
        return $this->hasMany(User::class, 'id');
    }
}
