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
            $table->unsignedBigInteger('account_id');
            $table->foreign('account_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('contact')->nullable();
            $table->string('address')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('department')->nullable();
            $table->string('company')->nullable();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->foreign('role_id')->references('id')->on('tbl_roles')->onUpdate('cascade');
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
