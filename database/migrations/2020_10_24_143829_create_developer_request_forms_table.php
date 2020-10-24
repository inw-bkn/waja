<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeveloperRequestFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('developer_request_forms', function (Blueprint $table) {
            $table->id();
            $table->string('detail', 1024);
            $table->unsignedBigInteger('requester');
            $table->foreign('requester')->references('id')->on('users');
            $table->string('status')->default('pending');
            $table->string('rejected_note')->nullable();
            $table->unsignedBigInteger('approver')->nullable();
            $table->foreign('approver')->references('id')->on('users');
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
        Schema::dropIfExists('developer_request_forms');
    }
}
