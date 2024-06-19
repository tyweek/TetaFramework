<?php

namespace App\Models;

use TetaFramework\Database\Model;

class Test extends Model
{
    protected $table = 'Tests';
    protected $fillable = ["name"];
    protected $uniqueField = "name";
}
