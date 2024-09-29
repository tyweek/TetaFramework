<?php

namespace TetaFramework\Database;

use PDO;
use PDOException;

class DatabaseManager
{
    protected static $instance = null;
    protected static $globalInstance = null;
    protected $connection;

    // Constructor privado para evitar instanciación directa
    private function __construct() {}

    // Obtiene la instancia singleton de la clase
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Añade una conexión a la base de datos
    public static function addConnection($config)
    {
        self::getInstance()->connect($config);
    }

    // Método protegido para conectar a la base de datos
    protected function connect($config)
    {
        $host = $config['host'];
        $db = $config['database'];
        $user = $config['username'];
        $pass = $config['password'];
        $charset = $config['charset'];
        $collation = $config['collation'] ?? 'utf8_unicode_ci';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->connection = new PDO($dsn, $user, $pass, $options);
            $this->connection->exec("set names $charset collate $collation");
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    // Obtiene la conexión actual
    public static function connection()
    {
        return self::getInstance()->getConnection();
    }

    // Método protegido para obtener la conexión
    protected function getConnection()
    {
        return $this->connection;
    }

    // Crea un nuevo QueryBuilder para la tabla especificada
    public static function table($table)
    {
        return new QueryBuilder(self::connection(), $table);
    }

    // Crea un nuevo SchemaBuilder
    public static function schema()
    {
        return new SchemaBuilder(self::connection());
    }

    // Establece la instancia como global
    public static function setAsGlobal()
    {
        self::$globalInstance = self::getInstance();
    }

    // Obtiene la instancia global
    public static function getGlobalInstance()
    {
        return self::$globalInstance;
    }

    // Inicializa Eloquent, si es necesario
    public static function bootEloquent()
    {
        // Métodos relacionados con Eloquent, si es necesario
    }
}
