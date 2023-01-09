<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriorityColumnToTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign('tasks_priority_id_foreign');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('priority_id');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->enum('priority',[
                'минимальный',
                'средний',
                'критический',
            ])->default('средний');
        });

        Schema::dropIfExists('priorities');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
