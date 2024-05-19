<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatesessionsTable extends Migration
{
    public function up()
    {
       try{
            Capsule::schema()->create('sessions', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('user_id');
                $table->string('token');
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
       } catch (\Throwable $th) {
      }
    }

    public function down()
    {
        Capsule::schema()->dropIfExists('sessions');
    }
}
