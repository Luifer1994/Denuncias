<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'url',
        'id_complaint'
    ];


    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'id_complaint');
    }
}
