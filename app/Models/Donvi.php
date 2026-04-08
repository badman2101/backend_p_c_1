<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donvi extends Model
{
    use HasFactory;

    protected $table = 'donvi';

    protected $fillable = [
        'ten_don_vi',
    ];
}
