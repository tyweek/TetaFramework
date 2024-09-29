<?php

namespace TetaFramework\Database;

use PDO;
use PDOException;

class SchemaBuilder
{
    protected $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function create($table, callable $callback)
    {
        if ($this->tableExists($table)) {
            echo "Table '$table' already exists. Skipping creation.\n";
            return;
        }
        $blueprint = new Blueprint($table);
        $callback($blueprint);

        $this->connection->exec($blueprint->toSql());
    }

    public function drop($table)
    {
        $this->connection->exec("DROP TABLE IF EXISTS $table");
    }
    
    public function dropIfExists($table)
    {
        $this->connection->exec("DROP TABLE IF EXISTS $table");
    }

    // Otros métodos para manejar la estructura de la base de datos
    public function rename($oldTable, $newTable)
    {
        $this->connection->exec("ALTER TABLE $oldTable RENAME TO $newTable");
    }

    public function addColumn($table, $columnDefinition)
    {
        $this->connection->exec("ALTER TABLE $table ADD $columnDefinition");
    }

    public function dropColumn($table, $column)
    {
        $this->connection->exec("ALTER TABLE $table DROP COLUMN $column");
    }

    public function modifyColumn($table, $columnDefinition)
    {
        $this->connection->exec("ALTER TABLE $table MODIFY $columnDefinition");
    }

    public function addIndex($table, $indexName, $columns)
    {
        $columnsList = implode(', ', (array) $columns);
        $this->connection->exec("CREATE INDEX $indexName ON $table ($columnsList)");
    }

    public function dropIndex($table, $indexName)
    {
        $this->connection->exec("DROP INDEX $indexName ON $table");
    }

    protected function tableExists($table)
    {
        try {
            $result = $this->connection->query("SELECT 1 FROM $table LIMIT 1");
        } catch (PDOException $e) {
            return false;
        }
        return $result !== false;
    }
}
