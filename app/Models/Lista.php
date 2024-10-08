<?php

namespace App\Models;

use TetaFramework\Database\Model;

class Lista extends Model
{
    protected $table = 'listas'; // Nombre de la tabla en la base de datos
    protected $fillable = ['id','name', 'created_at', 'updated_at']; // Campos que se pueden llenar

}

