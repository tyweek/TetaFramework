<?php

namespace App\Models;

use TetaFramework\Database\Model;
class Roles extends Model
{
    protected $table = 'sessions';
    protected $fillable = [
        'name'
    ];
}
