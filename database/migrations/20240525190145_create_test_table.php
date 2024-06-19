<?php


use TetaFramework\Database\DatabaseManager;
use TetaFramework\Database\Blueprint;
use TetaFramework\Database\Migrations\Migration;

class CreateTestTable extends Migration
{
    public function up()
    {
       try{
            DatabaseManager::schema()->create('tests', function (Blueprint $table) {
            $table->increments('id');
            $table->string("name");
            $table->timestamps();
        });
       } catch (\Throwable $th) {
      }
    }

    public function down()
    {
        DatabaseManager::schema()->dropIfExists('tests');
    }
}
