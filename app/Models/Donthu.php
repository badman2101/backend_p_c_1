<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donthu extends Model
{
    use HasFactory;
    protected $table = 'donthu';
    protected $dates = ['ngay_tiep_nhan', 'han_xu_ly'];
    protected $fillable = ['tieu_de', 'phan_loai', 'nguon_tin', 'information_nguoiguidon', 'noi_dung_don', 'can_bo_thu_ly', 'ket_qua_xu_ly', 'ngay_tiep_nhan', 'han_xu_ly', 'trang_thai'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
