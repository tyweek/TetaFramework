<?php

namespace TetaFramework\Database;

class Model
{
    protected $table;
    protected $fillable = [];
    protected $attributes = ['id' => null];
    protected $queryBuilder;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder(DatabaseManager::connection(), $this->table);
    }

    public function getTable()
    {
        return $this->table;
    }

    public function all()
    {
        $results = $this->queryBuilder->select()->get();

        $this->attributes = array_map(function($result) {
            $lista = new static();
            $lista->fill((array)$result);
            return $lista;
        }, $results);

        return $this->attributes; 
    }

    public function where($column, $operator, $value)
    {
        $this->queryBuilder->where($column, $operator, $value);
        return $this; 
    }

    public function orWhere($column, $operator, $value)
    {
        $this->queryBuilder->orWhere($column, $operator, $value);
        return $this; 
    }

    public function between($column, $start, $end)
    {
        $this->queryBuilder->between($column, $start, $end);
        return $this; 
    }

    public function find($id)
    {
        $result = $this->queryBuilder->select()->where('id', '=', $id)->get();

        if ($result) {
            $lista = new static();
            $lista->fill((array)$result[0]);
            return $lista;
        }

        return null;
    }

    public function create(array $data)
    {
        $data = array_intersect_key($data, array_flip($this->fillable));
        $id = $this->queryBuilder->insert($data);
        return $this->find($id);
    }

    public function update(array $data)
    {
        $data = array_intersect_key($data, array_flip($this->fillable));
        $this->queryBuilder->update($data, $this->attributes['id']);
    }

    public function delete()
    {
        $this->queryBuilder->delete($this->attributes['id']);
    }

    public function fill(array $data)
    {
        foreach ($data as $key => $value) {
            // Asigna siempre el id, aunque no esté en fillable
            if ($key === 'id') {
                $this->attributes['id'] = $value;
            } elseif (in_array($key, $this->fillable)) {
                $this->{$key} = $value;
            }
        }
    }
    
    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }

    // Agrega el método get() para delegar al QueryBuilder
    public function get($modelClass = null)
    {
        // Obtener los resultados de la consulta
        $results = $this->queryBuilder->get();

        if ($modelClass) {
            // Crear instancias del modelo
            return array_map(function($data) use ($modelClass) {
                $modelInstance = new $modelClass();
                $modelInstance->fill($data);
                return $modelInstance;
            }, $results);
        }

        return $results; // Si no hay modelo, devuelve el array normal
    }

    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    public function hasOne($relatedModel, $foreignKey, $localKey = 'id')
    {
        $relatedModelInstance = new $relatedModel();
        return $relatedModelInstance->where($foreignKey, '=', $this->{$localKey})->get($relatedModel)[0] ?? null;
    }

    public function hasMany($relatedModel, $foreignKey, $localKey = 'id')
    {
        $relatedModelInstance = new $relatedModel();
        // Realiza la consulta y devuelve las instancias del modelo relacionado
        return $relatedModelInstance->where($foreignKey, '=', $this->{$localKey})->get($relatedModel);
    }
   
    public function belongsTo($relatedModel, $foreignKey, $ownerKey = 'id')
    {
        $relatedModelInstance = new $relatedModel();
        // Realiza la consulta y devuelve la instancia del modelo relacionado
        return $relatedModelInstance->where($ownerKey, '=', $this->{$foreignKey})->get($relatedModel)[0] ?? null;
    }

    public function belongsToMany($relatedModel, $pivotTable, $foreignKey, $relatedForeignKey, $localKey = 'id', $relatedKey = 'id')
    {
        $relatedModelInstance = new $relatedModel();

        // Hacer la consulta a la tabla pivote
        $this->queryBuilder->select([$relatedModelInstance->getTable().'.*'])
                        ->from($pivotTable)
                        ->join($relatedModelInstance->getTable(), "{$relatedModelInstance->getTable()}.$relatedKey", '=', "$pivotTable.$relatedForeignKey")
                        ->where("$pivotTable.$foreignKey", '=', $this->{$localKey});
        
        // Devuelve una lista de instancias del modelo relacionado
        return $relatedModelInstance->get($relatedModel);
    }

    

}
