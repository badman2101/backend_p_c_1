<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vuan extends Model
{
    use HasFactory;

    protected $table = 'vuan';

    protected $fillable = [
        'ngay_khoi_to',
        'noi_dung',
        'so_luong_bi_can',
        'thong_tin_bi_can',
        'bien_phap_ngan_chan',
        'can_bo_thu_ly',
        'can_bo_huong_dan',
        'ket_qua',
        'kho_khan',
    ];

    protected $casts = [
        'ngay_khoi_to' => 'date',
    ];
}
