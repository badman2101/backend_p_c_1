<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Complains extends Model
{
    use HasFactory;

    protected $table = 'complaints';

    protected $fillable = [
        'title',
        'content',
        'type',
        'assigned_to',
        'status',
        'deadline',
        'result',
    ];


    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    
}
