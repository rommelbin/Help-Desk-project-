<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();

            $table->timestamps();

            $table->integer('size');
            $table->string('path');
            $table->string('name');
            $table->string('extension');  //  .jpg, .png, .docx, .xls, .xlsx.

            $table->foreignId('comment_id');
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
