<?php

namespace TetaFramework\Database;

use PDO;
use PDOException;

class QueryBuilder
{
    protected $connection;
    protected $table;
    protected $query;
    protected $bindings = [];
    protected $whereClause = '';
    protected $wheres = [];
    protected $orWheres = [];


    public function __construct(PDO $connection, $table)
    {
        $this->connection = $connection;
        $this->table = $table;
    }

    public function select($columns = ['*'])
    {
        $columns = implode(', ', $columns);
        $this->query = "SELECT $columns FROM {$this->table}";
        return $this;
    }

    public function where($column, $operator, $value)
    {
        if($operator == 'LIKE')
            $value = "".$value."";
        if ($this->whereClause) {
            $this->whereClause .= " AND $column $operator ?";
        } else {
            $this->whereClause = " WHERE $column $operator ?";
        }
        $this->bindings[] = $value;
        return $this;
    }
    public function whereIn($column, $values)
    {
        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        if ($this->whereClause) {
            $this->whereClause .= " AND $column IN ($placeholders)";
        } else {
            $this->whereClause = " WHERE $column IN ($placeholders)";
        }
        $this->bindings = array_merge($this->bindings, $values);
        return $this;
    }

    public function orWhere($column, $operator, $value)
    {
        if($operator == 'LIKE')
            $value = "".$value."";
        
        if ($this->whereClause) {
            $this->whereClause .= " OR $column $operator ?";
        } else {
            $this->whereClause = " WHERE $column $operator ?";
        }
        $this->bindings[] = $value;
        return $this;
    }

    public function count()
    {
        try {
            // Construimos la consulta para obtener el conteo
            $countQuery = "SELECT COUNT(*) as count FROM " . $this->table;
            $stmt = $this->connection->prepare($countQuery);
            $stmt->execute($this->bindings);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            // Log the error message
            error_log('QueryBuilder count error: ' . $e->getMessage());
            return false;
        }
    }
    public function paginate($perPage = 10, $page = 1)
    {
        // Calculamos el índice de inicio
        $startIndex = ($page - 1) * $perPage;

        // Agregamos el desplazamiento y el límite a la consulta
        $this->query .= " LIMIT $perPage OFFSET $startIndex";

        return $this;
    }

    public function get()
    {
        try {
            $sql = $this->query . $this->whereClause;
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($this->bindings);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            // Log the error message
            error_log('QueryBuilder get error: ' . $e->getMessage());
            return false;
        }
    }
    public function getArray()
    {
        try {
            $sql = $this->query . $this->whereClause;
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($this->bindings);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log the error message
            error_log('QueryBuilder get error: ' . $e->getMessage());
            return false;
        }
    }

    public function first()
    {
        try {
            $sql = $this->query . $this->whereClause . " LIMIT 1";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($this->bindings); 
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log the error message
            error_log('QueryBuilder first error: ' . $e->getMessage());
            return false;
        }
    }
    public function firstObj()
    {
        try {
            $sql = $this->query . $this->whereClause . " LIMIT 1";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($this->bindings); 
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            // Log the error message
            error_log('QueryBuilder first error: ' . $e->getMessage());
            return false;
        }
    }

    public function insert(array $data)
    {
        try {
            $columns = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            $this->query = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
            $this->bindings = array_values($data);
            $stmt = $this->connection->prepare($this->query);
            $stmt->execute($this->bindings);
            return $this->connection->lastInsertId();
        } catch (PDOException $e) {
            // Log the error message
            error_log('QueryBuilder insert error: ' . $e->getMessage());
            return false;
        }
    }

    public function update(array $data)
    {
        try {
            $setClause = implode(', ', array_map(fn($col) => "$col = ?", array_keys($data)));
            $this->query = "UPDATE {$this->table} SET $setClause" . $this->whereClause;
            $this->bindings = array_merge(array_values($data), $this->bindings);
            $stmt = $this->connection->prepare($this->query);
            return $stmt->execute($this->bindings);
        } catch (PDOException $e) {
            // Log the error message
            error_log('QueryBuilder update error: ' . $e->getMessage());
            return false;
        }
    }

    public function delete()
    {
        try {
            $this->query = "DELETE FROM {$this->table}" . $this->whereClause;
            $stmt = $this->connection->prepare($this->query);
            return $stmt->execute($this->bindings);
        } catch (PDOException $e) {
            // Log the error message
            error_log('QueryBuilder delete error: ' . $e->getMessage());
            return false;
        }
    }
    public function toSql()
    {
        $query = $this->query . $this->whereClause;
        foreach($this->bindings as $val)
        {
            $query = str_replace("?","'$val'",$query);
        }
        return $query;
    }
}
