<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintType extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
    ];
    protected $hidden = [
        'state',
        'created_at',
        'updated_at'
    ];

    public function complaint()
    {
        return $this->hasMany(Complaint::class, 'id_complaint_type');
    }
}
