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

    public function select($columns = ['*'])
    {
        $this->queryBuilder->select($columns);
        return $this; 
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

    public function sum($column,$alias)
    {
        $this->queryBuilder->sum($column, $alias);
        return $this;
    }

    public function groupBy($column)
    {
        $this->queryBuilder->groupBy($column);
        return $this;
    }

    public function orderBy($column)
    {
        $this->queryBuilder->orderBy($column);
        return $this;
    }
    public function count($column = '*')
    {
        $this->queryBuilder->count($column);
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

    public function findBy($column, $value)
    {
        // Realiza una consulta para obtener un solo resultado
        $result = $this->queryBuilder->select()->where($column, '=', $value)->getOne();

        if ($result) {
            // Si se encuentra un resultado, llenamos una instancia del modelo con los datos
            $modelInstance = new static();
            $modelInstance->fill((array)$result);
            return $modelInstance;
        }

        return null; // Retorna null si no se encuentra nada
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
                if(in_array($key,$this->fillable))
                    $this->{$key} = $value;
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
    public function getOne($modelClass = null)
    {
        // Obtener un solo resultado de la consulta
        $result = $this->queryBuilder->getOne();

        if ($modelClass && $result) {
            // Crear una instancia del modelo si se especifica la clase de modelo
            $modelInstance = new $modelClass();
            $modelInstance->fill($result);
            return $modelInstance;
        }

        return $result; // Si no hay modelo, devuelve el resultado como array
    }
    
    public function paginate($perPage = 15, $currentPage = 1)
    {
        $offset = ($currentPage - 1) * $perPage;
        
        // Obtener los resultados con limit y offset
        $results = $this->queryBuilder->select()
                                      ->limit($perPage)
                                      ->offset($offset)
                                      ->get();

        // Obtener el total de registros para calcular la paginación
        $totalItems = $this->queryBuilder->select(['COUNT(*) as count'])->getOne()['count'];
        $totalPages = ceil($totalItems / $perPage);

        return [
            'data' => array_map(function($result) {
                $modelInstance = new static();
                $modelInstance->fill((array)$result);
                return $modelInstance;
            }, $results),
            'pagination' => [
                'total_items' => $totalItems,
                'per_page' => $perPage,
                'current_page' => $currentPage,
                'total_pages' => $totalPages
            ]
        ];
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
