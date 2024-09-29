<?php

use TetaFramework\Database\Blueprint;
use TetaFramework\Database\Migrations\Migration;
use TetaFramework\Database\DatabaseManager;

class CreateRolesTable extends Migration{
    
    public function up()
    {
        try {
            DatabaseManager::schema()->create('roles', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->timestamps();
            });
        } catch (\Throwable $th) {
            throw $th;
        }
       
    }

    public function down()
    {
        DatabaseManager::schema()->dropIfExists('roles');
    }
}