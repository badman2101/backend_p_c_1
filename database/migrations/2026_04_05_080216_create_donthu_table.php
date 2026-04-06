<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonthuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donthu', function (Blueprint $table) {
            $table->id();
            $table->string('tieu_de')->nullable();
            $table->string('phan_loai')->nullable();
            $table->string('nguon_tin')->nullable();
            $table->string('information_nguoiguidon')->nullable();
            $table->text('noi_dung_don')->nullable();
            $table->string('can_bo_thu_ly')->constrained('users')->nullable();
            $table->string('ket_qua_xu_ly')->nullable();
            $table->date('ngay_tiep_nhan')->nullable();
            $table->date('han_xu_ly')->nullable();
            $table->string('trang_thai')->nullable(); 
            $table->text('kho_khan')->nullable();
            $table->timestamps();
        });
    }
}
