
# Documentación de la Clase View

La clase `View` es una parte fundamental del sistema de plantillas en el framework `TetaFramework`. Se encarga de asignar variables, procesar directivas y renderizar vistas. A continuación se detallan sus métodos y su uso.

## Métodos de la Clase View

### 1. `assign($key, $value)`
Asigna una variable que puede ser utilizada en la vista.

#### Parámetros:
- **string $key**: La clave de la variable.
- **mixed $value**: El valor de la variable.

#### Ejemplo:
```php
View::assign('nombre', 'Brando');
```

### 2. `clear()`
Limpia todas las variables almacenadas, dejándolas vacías.

#### Ejemplo:
```php
View::clear();
```

### 3. `render($view)`
Renderiza una vista y devuelve el contenido HTML generado.

#### Parámetros:
- **string $view**: El nombre de la vista a renderizar (sin la extensión .php).

#### Retorno:
- **string**: El contenido HTML renderizado de la vista.

#### Excepción:
- Lanza una excepción si se detecta un bucle infinito.

#### Ejemplo:
```php
$content = View::render('mi_vista');
```

## Procesamiento de Directivas

La clase `View` también incluye métodos para procesar varias directivas dentro de las vistas:

### 4. `processDirectives($content)`
Procesa las directivas en el contenido de la vista.

### 5 `processImport($content)`
Procesa la directiva `@import` para importar otras vistas a la actual.

### 6. `processPhpDirectives($content)`
Convierte las directivas de PHP como `{{ variable }}` en código PHP correspondiente.

### 7. `processForeach($content)`
Convierte la directiva `@foreach` en código PHP de bucle foreach.

### 8. `processIf($content)`
Convierte la directiva `@if` en código PHP de condicional if.

### 9. `processElse($content)`
Convierte la directiva `@else` en código PHP correspondiente.

### 10. `processFor($content)`
Convierte la directiva `@for` en código PHP de bucle for.

### 11. `processPhpCode($content)`
Convierte las directivas `@php ... @endphp` en código PHP ejecutable.

## Ejemplo de Uso en el Controlador `ListaController`

A continuación se muestra un ejemplo de cómo se utiliza la clase `View` en el controlador `ListaController`.

```php
namespace App\Controllers;

use App\Models\Lista;
use TetaFramework\Http\Response;
use TetaFramework\Http\Request;
use TetaFramework\View;

class ListaController extends Controller
{
    public function index(Request $request): Response
    {
        $items = (new Lista())->where('name', 'LIKE', '%brando%')->get(Lista::class);
        
        View::assign('items', $items);
        View::assign('lang', $this->getLang()->getAllTranslate());
        View::assign('uri', $request->getPathInfo());
        View::assign('locale', $this->getLang()->getLocale());
        
        $content = View::render('Lista');
        return new Response($content);
    }
}
```

## Ejemplo de uso en la vista "Lista"
```php
<h1>{{ $title }}</h1>

@foreach ($items as $item)
    <p>{{ $item->name }}</p>
    @foreach ($item->details as $detail)
        <span>{{ $detail->data }}</span>
    @endforeach
@endforeach

@if ($showFooter)
    <footer>Este es el pie de página.</footer>
@else
    <p>No se muestra el pie de página.</p>
@endif

@for ($i = 0; $i <= 10; $i++)
    <p>Iteración: {{ $i }}</p>
@endfor

@php
    // Código PHP adicional
    echo "Este es un mensaje desde PHP";
@endphp

// importa otra vista
@import->test 

```

## Conclusión
La clase `View` facilita el manejo de variables y la renderización de vistas en el framework `TetaFramework`, permitiendo un control efectivo sobre las plantillas y su contenido.
