<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditEnumAttributesInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('role');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', [
                'Ожидание',
                'Активен',
                "Просрочено",
            ])->default('Ожидание');
            $table->enum('role', [
                'Заказчик',
                'Администратор',
                "Исполнитель"
            ])->default('Заказчик');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
