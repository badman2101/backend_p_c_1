<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVuanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vuan', function (Blueprint $table) {
            $table->id();
            $table->date('ngay_khoi_to')->nullable();
            $table->text('noi_dung')->nullable();
            $table->string('so_luong_bi_can')->nullable();
            $table->text('thong_tin_bi_can')->nullable();
            $table->string('can_bo_thu_ly')->constrained('users')->nullable();
            $table->string('can_bo_huong_dan')->constrained('users')->nullable();
            $table->string('ket_qua')->nullable();
            $table->text('kho_khan')->nullable();
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
        Schema::dropIfExists('vuan');
    }
}
