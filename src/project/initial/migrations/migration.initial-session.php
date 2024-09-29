<?php

use TetaFramework\Database\Blueprint;
use TetaFramework\Database\Migrations\Migration;
use TetaFramework\Database\DatabaseManager;

class CreateSessionsTable extends Migration
{
    public function up()
    {
       try{
        DatabaseManager::schema()->create('sessions', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->string('token');
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
       } catch (\Throwable $th) {
         throw $th;
      }
    }

    public function down()
    {
        DatabaseManager::schema()->dropIfExists('sessions');
    }
}
