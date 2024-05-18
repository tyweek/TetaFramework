<?php

$config = require_once './config/database.php';

use Illuminate\Database\Capsule\Manager as Capsule;


$capsule = new Capsule;

$capsule->addConnection($config);

$capsule->setAsGlobal();
$capsule->bootEloquent();
