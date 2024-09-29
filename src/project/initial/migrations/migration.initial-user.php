<?php

use Illuminate\Database\Events\DatabaseBusy;
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
                $table->string('lastname');
                $table->string('email')->unique();
                $table->string('password');
                $table->integer('role_id')->default(1); //usuario nivel 1 (normal)
                $table->foreign('role_id')->references('id')->on('roles');
                $table->timestamps();
            });
            $this->insertDefaultRecords();

        } catch (\Throwable $th) {
            throw $th;
        }
       
    }

    public function down()
    {
        DatabaseManager::schema()->dropIfExists('users');
    }

    protected function insertDefaultRecords()
    {
        $connection = DatabaseManager::connection();

        // Asegúrate de que la tabla roles existe y tiene los registros necesarios
        $rolesExist = $connection->query("SELECT 1 FROM roles WHERE id = 1")->fetch();
        if (!$rolesExist) {
            $connection->exec("INSERT INTO roles (id, name) VALUES (1, 'usuario')");
            $connection->exec("INSERT INTO roles (id, name) VALUES (2, 'moderador')");
            $connection->exec("INSERT INTO roles (id, name) VALUES (3, 'administrador')");
        }

        // Insertar registros por defecto en la tabla users
        $connection->exec("INSERT INTO users (name, lastname, email, password, role_id) VALUES
            ('Admin', 'User', 'admin@example.com', '".password_hash('password', PASSWORD_DEFAULT)."', 3)"
        );
    }
}