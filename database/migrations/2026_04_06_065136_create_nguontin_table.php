<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNguontinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nguontin', function (Blueprint $table) {
            $table->id();
            $table->string("ngay_phan_cong")->nullable();
            $table->text("noi_dung")->nullable();
            $table->string("dieu_tra_vien")->nullable();
            $table->string("ket_qua")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nguontin');
    }
}
