<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponseComplaint extends Model
{
    protected $fillable = [
        'id',
        'description',
        'id_state_complaint',
        'id_user',
        'id_complaint',
    ];
    use HasFactory;
    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'id_complaint');
    }

    public function MediaResponse()
    {
        return $this->hasMany(MediaResponse::class, 'id_response');
    }
}
