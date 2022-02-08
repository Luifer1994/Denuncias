<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaResponse extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'url',
        'id_response'
    ];

    public function response()
    {
        return $this->belongsTo(ResponseComplaint::class, 'id_response');
    }
}
