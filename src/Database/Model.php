<?php

namespace TetaFramework\Database;

use TetaFramework\Database\DatabaseManager;
use TetaFramework\Database\Pagination;

class Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $attributes = [];
    protected $exists = false;
    protected $uniqueField;

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->exists = false; // Inicializar como false
    }
    

    public static function query()
    {
        return new QueryBuilder(DatabaseManager::connection(), (new static)->getTable());
    }

    public static function paginate($perPage = 10, $page = 1)
    {
        // Obtenemos los resultados paginados
        $items = static::query()->select()->paginate($perPage, $page)->getArray();

        // Obtenemos el total de registros
        $totalItems = static::query()->count();
        // Creamos una instancia de Pagination
        $pagination = new Pagination($totalItems, $perPage, $page);

        // Devolvemos un array con los resultados y la instancia de Pagination
        return [
            'result' => $items,
            'pagination' => $pagination
        ];
    }

    public static function all($perPage = 1, $currentPage = 1)
    {
        return static::query()->select()->get();
    }
    public static function allArray($perPage = 1, $currentPage = 1)
    {
        return static::query()->select()->getArray();
    }

    public static function find($id)
    {
        $result = static::query()->select()->where((new static)->getPrimaryKey(), '=', $id)->first();
        if ($result) {
            $model = new static($result); // Crear una nueva instancia de Test con los datos obtenidos
            $model->exists = true; // Marcar el modelo como existente
            return $model;
        }
        return null;
    }

    // public function save()
    // {
    //     if ($this->exists) {
    //         return static::query()->where($this->getPrimaryKey(), '=', $this->getKey())->update($this->attributes);
    //     } else {
    //         $id = static::query()->insert($this->attributes);
    //         if ($id) {
    //             $this->exists = true;
    //             $this->setKey($id);
    //         }
    //         return $id;
    //     }
    // }

  
    public function save()
    {
        // Verificar si el modelo ya existe en la base de datos
        if ($this->exists) {
            // Si el modelo ya existe, actualizamos los datos
            return static::query()->select()->where($this->getPrimaryKey(), '=', $this->getKey())->update($this->attributes);
        } else {
            $existingModel = static::query()->select()
            ->where($this->uniqueField, '=', $this->{$this->uniqueField}) // Suponiendo que 'name' es el campo único
            ->first();
            if ($existingModel) {
                // Si el campo único ya existe, arrojar un error
                echo "El valor del campo único '{$this->{$this->uniqueField}}' ya existe en la base de datos.";
            } else {
                // Si el campo único no existe, continuar con la lógica de guardar el modelo
                $id = static::query()->insert($this->attributes);
                if ($id) {
                    $this->exists = true;
                    $this->setKey($id);
                }
                return $id;
            }
        }
    }
    
    public function update(){
        $this->save();
    }

    public function delete()
    {
        if ($this->exists) {
            return static::query()->where($this->getPrimaryKey(), '=', $this->getKey())->delete();
        }
        return false;
    }

    public static function create(array $attributes)
    {
        $model = new static($attributes);
        $created = $model->save();
        return $created;
    }

    // public static function where($column, $value)
    // {
    //     $result = static::query()->select()->where($column, "=", $value)->first();
    //     if ($result) {
    //         $model = new static($result); // Crear una nueva instancia de Test con los datos obtenidos
    //         $model->exists = true; // Marcar el modelo como existente
    //         return $model;
    //     }
    //     return null;
    // }
    public static function where($column, $operator = null, $value = null)
    {
        // Si solo se pasan dos argumentos, asumimos que el operador es "="
        if (func_num_args() == 2) {
            $value = $operator;
            $operator = "=";
        }

        // Realizar la consulta usando los argumentos proporcionados
        $query = static::query()->select()->where($column, $operator, $value);
        $result = $query->first();
        
        if ($result) {
            $model = new static($result); // Crear una nueva instancia del modelo con los datos obtenidos
            $model->exists = true; // Marcar el modelo como existente
            return $model;
        }

        return null;
    }
    
    public static function allwhere($column, $operator = null, $value = null)
    {
        // Si solo se pasan dos argumentos, asumimos que el operador es "="
        if (func_num_args() == 2) {
            $value = $operator;
            $operator = "=";
        }

        // Realizar la consulta usando los argumentos proporcionados
        $query = static::query()->select()->where($column, $operator, $value);
        
        return $query->getArray();
    }

    public static function andallwhere($column, $operator = null, $value = null,
    $andcol = null,$andopr = null,$anvalue = null)
    {
        // Si solo se pasan dos argumentos, asumimos que el operador es "="
        if (func_num_args() == 2) {
            $value = $operator;
            $operator = "=";
        }

        // Realizar la consulta usando los argumentos proporcionados
        $query = static::query()->select()->where($column, $operator, $value)
        ->where($andcol,$andopr,$anvalue);
        
        return $query->getArray();
    }


    protected function getTable()
    {
        return $this->table ?? strtolower((new \ReflectionClass($this))->getShortName()) . 's';
    }

    protected function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    protected function getKey()
    {
        return $this->attributes[$this->getPrimaryKey()] ?? null;
    }

    protected function setKey($value)
    {
        $this->attributes[$this->getPrimaryKey()] = $value;
    }

    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    // Métodos de relación

    public function hasOne($related, $foreignKey = null, $localKey = null)
    {
        $relatedInstance = new $related;
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $localKey = $localKey ?: $this->primaryKey;
        return $relatedInstance->query()->select()->where($foreignKey,'=', $this->{$localKey})->firstObj();
    }

    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $relatedInstance = new $related;
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $localKey = $localKey ?: $this->primaryKey;
        return $relatedInstance->query()->where($foreignKey,'=', $this->{$localKey})->get();
    }

    public function belongsTo($related, $foreignKey = null, $ownerKey = null)
    {
        $relatedInstance = new $related;
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $ownerKey = $ownerKey ?: $relatedInstance->primaryKey;
        return $relatedInstance->query()->where($ownerKey,'=', $this->{$foreignKey})->first();
    }

    public function belongsToMany($related, $pivotTable, $foreignPivotKey, $relatedPivotKey, $localKey = null, $relatedKey = null)
    {
        $relatedInstance = new $related;
        $localKey = $localKey ?: $this->primaryKey;
        $relatedKey = $relatedKey ?: $relatedInstance->primaryKey;

        $pivotTable = (new QueryBuilder($this->connection, $pivotTable))
            ->select([$foreignPivotKey, $relatedPivotKey])
            ->where($foreignPivotKey,'=', $this->{$localKey})
            ->getArray();

        $relatedIds = array_column($pivotTable, $relatedPivotKey);

        return $relatedInstance->query()->whereIn($relatedKey, $relatedIds)->get();
    }

    protected function getForeignKey()
    {
        var_dump(strtolower((new \ReflectionClass($this))->getShortName()) . '_id');
        return strtolower((new \ReflectionClass($this))->getShortName()) . '_id';
    }
}
