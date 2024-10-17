<?php

namespace TetaFramework\Database;

use PDO;

class QueryBuilder
{
    protected $connection;
    protected $table;
    protected $query;
    protected $bindings = [];
    protected $limit;
    protected $offset;

    public function __construct(PDO $connection, $table)
    {
        $this->connection = $connection;
        $this->table = $table;
        $this->query = '';
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    public function from($table)
    {
        $this->table = $table;
        return $this;
    }

    // Método para realizar combinaciones entre tablas
    public function join($table, $first, $operator, $second, $type = 'INNER')
    {
        $this->query .= " $type JOIN $table ON $first $operator $second";
        return $this;
    }

    public function select($columns = ['*'])
    {
        
        $this->query = 'SELECT ' . implode(', ', $columns) . ' FROM ' . $this->table;
        // var_dump($this->query);
        // die();
        return $this;
    }

    public function where($column, $operator, $value, $condition = 'AND')
    {
        if (strpos($this->query, 'WHERE') === false) {
            $this->query .= " WHERE $column $operator :$column";
        } else {
            $this->query .= " $condition $column $operator :$column";
        }
        $this->bindings[$column] = $value;
        return $this;
    }

    public function orWhere($column, $operator, $value)
    {
        return $this->where($column, $operator, $value, 'OR');
    }

    public function between($column, $start, $end)
    {
        if (strpos($end, ' ') === false) {
            $end .= ' 23:59:59';
        }

        if (strpos($this->query, 'WHERE') === false) {
            $this->query .= " WHERE $column BETWEEN :start AND :end";
        } else {
            $this->query .= " AND $column BETWEEN :start AND :end";
        }

        $this->bindings['start'] = $start;
        $this->bindings['end'] = $end;
        return $this;
    }

    public function sum1($column, $alias = 'total')
    {
        $this->query = "SELECT SUM($column) as $alias FROM " . $this->table;
        return $this;
    }

    public function sum($column, $alias = 'total')
    {
        if (strpos($this->query, 'SELECT') !== false) {
            // Si ya hay un SELECT, agregar el SUM como otra columna
            $this->query = str_replace('SELECT', 'SELECT SUM('. $column . ') as ' . $alias .', ', $this->query);
        } else {
            // Si no hay un SELECT, crearlo con SUM
            $this->query = 'SELECT SUM(' . $column . ') as ' . $alias. ' FROM '.$this->table;
        }
    
        return $this;
    }

    public function groupBy($column)
    {
        $this->query .= " GROUP BY $column";
        return $this;
    }

    public function orderBy($column)
    {
        $this->query .= " ORDER BY $column";
        return $this;
    }
    
    public function count($column = '*', $alias = 'total')
    {
        $this->query = "SELECT COUNT($column) as $alias FROM " . $this->table;
        return $this;
    }

    public function get($modelClass = null)
    {
        if (strpos($this->query, 'SELECT') === false) {
            $this->query = 'SELECT * FROM ' . $this->table . ' ' . $this->query;
        }

        // Agregar limit y offset a la consulta
        if ($this->limit) {
            $this->query .= ' LIMIT ' . $this->limit;
        }

        if ($this->offset) {
            $this->query .= ' OFFSET ' . $this->offset;
        }
        
        $stmt = $this->connection->prepare($this->query);
        $stmt->execute($this->bindings);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($modelClass) {
            return array_map(function($data) use ($modelClass) {
                $modelInstance = new $modelClass();
                $modelInstance->fill($data);
                return $modelInstance;
            }, $results);
        }

        return $results;
    }

    public function getOne($modelClass = null)
    {
        if (strpos($this->query, 'SELECT') === false) {
            $this->query = 'SELECT * FROM ' . $this->table . ' ' . $this->query;
        }
        
        $this->query .= ' LIMIT 1';

        $stmt = $this->connection->prepare($this->query);
        $stmt->execute($this->bindings);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($modelClass && $result) {
            $modelInstance = new $modelClass();
            $modelInstance->fill($result);
            return $modelInstance;
        }

        return $result;
    }

    public function insert(array $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $this->query = "INSERT INTO $this->table ($columns) VALUES ($placeholders)";

        $stmt = $this->connection->prepare($this->query);
        $stmt->execute($data);

        return $this->connection->lastInsertId();
    }

    public function update(array $data, $id)
    {
        $setString = implode(', ', array_map(fn($col) => "$col = :$col", array_keys($data)));
        $this->query = "UPDATE $this->table SET $setString WHERE id = :id";

        $data['id'] = $id;
        $stmt = $this->connection->prepare($this->query);
        $stmt->execute($data);
    }

    public function delete($id)
    {
        $this->query = "DELETE FROM $this->table WHERE id = :id";
        $stmt = $this->connection->prepare($this->query);
        $stmt->execute(['id' => $id]);
    }

    public function toSql()
    {
        return $this->query;
    }
}
