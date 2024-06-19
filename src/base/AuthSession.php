<?php

namespace TetaFramework\base;

use TetaFramework\Database\Model;

class AuthSession extends Model
{
    protected $table = 'sessions';
    protected $fillable = [
        'user_id', 'token','expires_at'
    ];
}
