<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConplaintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('title'); //tiêu đề
            $table->string('content'); //nội dung
            $table->string('type'); //loại khiếu nại
            $table->foreignId('assigned_to')->constrained('users'); //phân công người giải quyết    
            $table->string('status'); //trạng thái
            $table->date('deadline'); //ngày hết hạn
            $table->string('result'); //kết quả giải quyết
            $table->timestamps(); //ngày tạo và ngày cập nhật
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conplaints');
    }
}
