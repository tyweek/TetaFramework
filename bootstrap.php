<?php
error_reporting(E_ERROR | E_PARSE);
try {
    $config = require_once './../config/database.php';
} catch (\Throwable $th) {
    $config = require_once './config/database.php';
}

use Illuminate\Database\Capsule\Manager as Capsule;


$capsule = new Capsule;

$capsule->addConnection($config);

$capsule->setAsGlobal();
$capsule->bootEloquent();
