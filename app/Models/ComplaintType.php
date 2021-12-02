<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
