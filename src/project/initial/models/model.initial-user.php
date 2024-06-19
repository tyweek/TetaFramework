<?php

namespace App\Models;

use TetaFramework\Database\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['name',"lastname", 'email', 'password',"role_id"];

    
    public function Role()
    {
        return $this->hasOne(Role::class,'id','role_id')->name;
    }
    
}
