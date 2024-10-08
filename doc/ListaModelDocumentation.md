
# Ejemplo de Uso del Modelo: ListaController

Este ejemplo demuestra cómo utilizar el modelo `Lista` en un controlador típico para realizar varias operaciones como recuperar, filtrar, crear, actualizar y eliminar registros.

## Ejemplo: `ListaController`

```php
namespace App\Controllers;

use App\Models\Lista;

class ListaController
{
    public function index()
    {
        // Obtener todos los elementos
        $items = (new Lista())->all();

        // Filtrar elementos por condiciones
        $filteredItems = (new Lista())
            ->where('name', 'LIKE', '%brando%')
            ->orWhere('created_at', '>=', '2024-10-08')
            ->get();

        // Crear un nuevo elemento
        $newItem = (new Lista())->create(['name' => 'Nuevo Item']);

        // Actualizar un elemento existente
        (new Lista())->update(['name' => 'Item Actualizado'], 1);

        // Eliminar un elemento
        (new Lista())->delete(1);

        // Retornar los resultados
        return [
            'items' => $items,
            'filtered' => $filteredItems,
            'newItem' => $newItem,
        ];
    }
}
```

## Operaciones del Modelo

### 1. Obtener Todos los Elementos
Puedes obtener todos los registros del modelo `Lista` utilizando el método `all()`.

```php
$items = (new Lista())->all();
```

### 2. Filtrar Elementos
Usa los métodos `where` y `orWhere` para filtrar registros según condiciones específicas.

```php
$filteredItems = (new Lista())
    ->where('name', 'LIKE', '%brando%')
    ->orWhere('created_at', '>=', '2024-10-08')
    ->get();
```

### 3. Crear un Nuevo Elemento
Para crear un nuevo registro, puedes usar el método `create()`. Los datos pasados a este método solo incluirán los atributos definidos en el array `$fillable`.

```php
$newItem = (new Lista())->create(['name' => 'Nuevo Item']);
```

### 4. Actualizar un Elemento Existente
Para actualizar un registro existente, puedes usar el método `update()`. El modelo usará automáticamente su propio `id` para actualizar el registro, no es necesario pasar el `id` manualmente.

```php
$updatedItem = (new Lista())->find(1);
$updatedItem->update(['name' => 'Item Actualizado']);
```

### 5. Eliminar un Elemento
Para eliminar un registro, simplemente llama al método `delete()` en una instancia del modelo.

```php
$deletedItem = (new Lista())->find(1);
$deletedItem->delete();
```

## Conclusión
Este ejemplo muestra cómo el modelo `Lista` se utiliza para interactuar con una base de datos. Demuestra operaciones CRUD básicas (Crear, Leer, Actualizar, Eliminar), así como la filtración de registros utilizando condiciones como `where` y `orWhere`. Puedes basarte en este patrón para crear una lógica de manejo de datos más compleja dentro de tu aplicación.
