<?php

namespace TetaFramework\Database;

class Blueprint
{
    protected $table;
    protected $columns = [];
    protected $constraints = [];
    protected $foreignKeys = [];
    protected $lastColumn;
    protected $lastForeignKey;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function increments($column)
    {
        $this->columns[] = "$column INT AUTO_INCREMENT PRIMARY KEY";
        $this->lastColumn = $column;
        return $this;
    }

    public function string($column, $length = 255)
    {
        $this->columns[] = "$column VARCHAR($length)";
        $this->lastColumn = $column;
        return $this;
    }

    public function integer($column)
    {
        $this->columns[] = "$column INT";
        $this->lastColumn = $column;
        return $this;
    }
    public function decimal($column,$precision = 10, $scale = 2)
    {
        $this->columns[] = "$column DECIMAL($precision, $scale)";
        $this->lastColumn = $column;
        return $this;
    }
    public function unsignedBigInteger($column)
    {
        $this->columns[] = "$column BIGINT UNSIGNED";
        $this->lastColumn = $column;
        return $this;
    }

    public function BigInteger($column)
    {
        $this->columns[] = "$column BIGINT";
        $this->lastColumn = $column;
        return $this;
    }

    public function timestamp($column)
    {
        $this->columns[] = "$column TIMESTAMP";
        $this->lastColumn = $column;
        return $this;
    }

    public function nullable()
    {
        if ($this->lastColumn) {
            $this->columns[count($this->columns) - 1] .= " NULL";
        } else {
            throw new \Exception("No column defined before calling nullable()");
        }
        return $this;
    }

    public function nonullable()
    {
        if ($this->lastColumn) {
            $this->columns[count($this->columns) - 1] .= " NOT NULL";
        } else {
            throw new \Exception("No column defined before calling nonullable()");
        }
        return $this;
    }

    public function unique()
    {
        if ($this->lastColumn) {
            $this->constraints[] = "UNIQUE ({$this->lastColumn})";
        } else {
            throw new \Exception("No column defined before calling unique()");
        }
        return $this;
    }

    public function default($value)
    {
        if ($this->lastColumn) {
            $this->columns[count($this->columns) - 1] .= " DEFAULT '$value'";
        } else {
            throw new \Exception("No column defined before calling default()");
        }
        return $this;
    }

    public function foreign($column)
    {
        $this->lastForeignKey = $column;
        return $this;
    }

    public function references($column)
    {
        if ($this->lastForeignKey) {
            $this->foreignKeys[$this->lastForeignKey]['references'] = $column;
        } else {
            throw new \Exception("No foreign key defined before calling references()");
        }
        return $this;
    }

    public function on($table)
    {
        if ($this->lastForeignKey) {
            $this->foreignKeys[$this->lastForeignKey]['on'] = $table;
        } else {
            throw new \Exception("No foreign key defined before calling on()");
        }
        return $this;
    }

    public function onDelete($action)
    {
        if ($this->lastForeignKey) {
            $this->foreignKeys[$this->lastForeignKey]['onDelete'] = $action;
        } else {
            throw new \Exception("No foreign key defined before calling onDelete()");
        }
        return $this;
    }

    public function timestamps()
    {
        $this->columns[] = "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        return $this;
    }

    public function toSql()
    {
        $columns = implode(', ', $this->columns);
        $constraints = implode(', ', $this->constraints);
        $foreignKeys = '';
    
        foreach ($this->foreignKeys as $column => $details) {
            $foreignKey = "FOREIGN KEY ({$column}) REFERENCES {$details['on']}({$details['references']})";
            if (isset($details['onDelete'])) {
                $foreignKey .= " ON DELETE {$details['onDelete']}";
            }
            $foreignKeys .= ', ' . $foreignKey;
        }
    
        if (!empty($constraints)) {
            $columns .= ', ' . $constraints;
        }
        if (!empty($foreignKeys)) {
            $columns .= $foreignKeys;
        }
    
        return "CREATE TABLE {$this->table} ($columns)";
    }
    
}
