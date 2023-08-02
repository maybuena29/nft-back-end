<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_employee', function (Blueprint $table) {
            $table->id();
            $table->foreign('id')->references('id')->on('users')->onUpdate('cascade');
            $table->unsignedBigInteger('employee_number');
            $table->string('name');
            $table->string('contact');
            $table->unsignedBigInteger('position_id');
            $table->foreign('position_id')->references('id')->on('tbl_position')->onUpdate('cascade');
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
