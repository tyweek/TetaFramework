<?php

namespace App\Models;

use TetaFramework\Database\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password'];
}
 