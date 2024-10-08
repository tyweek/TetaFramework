<?php

namespace TetaFramework\Database;

use PDO;

class QueryBuilder
{
    protected $connection;
    protected $table;
    protected $query;
    protected $bindings = [];
    protected $queryBuilder;

    public function __construct(PDO $connection, $table)
    {
        $this->connection = $connection;
        $this->table = $table;
        $this->query = '';
    }

    public function select($columns = ['*'])
    {
        $this->query = 'SELECT ' . implode(', ', $columns) . ' FROM ' . $this->table;
        return $this;
    }

    public function where($column, $operator, $value, $condition = 'AND')
    {
        // Verifica si la consulta ya contiene 'WHERE'
        if (strpos($this->query, 'WHERE') === false) {
            // No hay 'WHERE', agregamos la cláusula 'WHERE' sin espacio adicional
            $this->query .= " WHERE $column $operator :$column";
        } else {
            // Ya hay un 'WHERE', así que simplemente agregamos el tipo de condición
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
        // Si el valor de $end no tiene hora, se le agrega '23:59:59'
        if (strpos($end, ' ') === false) {
            $end .= ' 23:59:59';
        }
    
        // Verifica si la consulta ya contiene 'WHERE'
        if (strpos($this->query, 'WHERE') === false) {
            // No hay 'WHERE', agregamos la cláusula 'WHERE' sin espacio adicional
            $this->query .= " WHERE $column BETWEEN :start AND :end";
        } else {
            // Ya hay un 'WHERE', agregamos 'AND'
            $this->query .= " AND $column BETWEEN :start AND :end";
        }
        
        // Agregar los valores de inicio y fin al arreglo de bindings
        $this->bindings['start'] = $start;
        $this->bindings['end'] = $end; 
    
        return $this; 
    }
    

    public function get($modelClass = null)
    {
        // Verifica si la consulta no tiene SELECT, agrégalo automáticamente
        if (strpos($this->query, 'SELECT') === false) {
            $this->query = 'SELECT * FROM ' . $this->table . ' ' . $this->query;
        }
    
        // Prepara y ejecuta la consulta
        $stmt = $this->connection->prepare($this->query);
        $stmt->execute($this->bindings);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC); // Asegúrate de obtener un array asociativo
    
        // Si se especifica una clase de modelo, convierte los resultados
        if ($modelClass) {
            return array_map(function($data) use ($modelClass) {
                $modelInstance = new $modelClass();
                $modelInstance->fill($data); // Aquí $data debe ser un array
                return $modelInstance;
            }, $results);
        }
    
        return $results;
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
