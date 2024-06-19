<?php

namespace TetaFramework\Database\Migrations;

abstract class Migration
{
    // Método para aplicar la migración
    abstract public function up();

    // Método para revertir la migración
    abstract public function down();

    // Ejecutar la migración hacia arriba
    public function runUp()
    {
        $this->up();
    }

    // Ejecutar la migración hacia abajo
    public function runDown()
    {
        $this->down();
    }
}
