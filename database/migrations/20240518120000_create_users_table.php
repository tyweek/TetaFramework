<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration{
    
    public function up()
    {
        try {
            Capsule::schema()->create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->timestamps();
            });
        } catch (\Throwable $th) {
            //throw $th;
        }
       
    }

    public function down()
    {
        Capsule::schema()->dropIfExists('users');
    }
}