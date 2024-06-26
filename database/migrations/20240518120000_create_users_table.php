<?php

use TetaFramework\Database\Blueprint;
use TetaFramework\Database\Migrations\Migration;
use TetaFramework\Database\DatabaseManager;

class CreateUsersTable extends Migration{
    
    public function up()
    {
        try {
            DatabaseManager::schema()->create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->timestamps();
            });
        } catch (\Throwable $th) {
            throw $th;
        }
       
    }

    public function down()
    {
        DatabaseManager::schema()->dropIfExists('users');
    }
}