<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->boolean('private')->default(false);

            $table->string('name', 100);
            $table->string('description', 300);

            $table->timestamp('deadline', 0)->nullable();
            $table->timestamp('completed_at', 0)->nullable();
            $table->timestamps();

            $table->foreignId('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('users')->onDelete('set null');// ->default(auth()->user()->id);

            $table->foreignId('executor_id')->nullable();
            $table->foreign('executor_id')->references('id')->on('users')->onDelete('set null');

            $table->foreignId('priority_id')->default(2);
            $table->foreign('priority_id')->references('id')->on('priorities')->onDelete('set null');

            $table->foreignId('status_id')->default(1);
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
