<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Nguontin extends Model
{
    use HasFactory;

    protected $table = "nguontin";

    protected $fillable  = [
        'ngay_phan_cong',
        'noi_dung',
        'dieu_tra_vien',
        'ket_qua'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
