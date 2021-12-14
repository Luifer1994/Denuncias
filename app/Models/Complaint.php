<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'cod',
        'latitude',
        'longitude',
        'address',
        'name_offender',
        'description',
        'id_complaint_type',
        'id_user',
        'id_state',
        'id_user_asigne',
        'id_user_inquest'
    ];

    public function media()
    {
        return $this->hasMany(Media::class, 'id_complaint');
    }

    public function ResponseComplaint()
    {
        return $this->hasMany(ResponseComplaint::class, 'id_complaint');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
