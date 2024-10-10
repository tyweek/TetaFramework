<?php

namespace TetaFramework\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class MakeAllCommand extends Command
{
    protected static $defaultName = 'make:all';

    protected function configure()
    {
        $this->setDescription('Create a new model, view, controller, and migration.')
             ->setHelp('This command allows you to create a new model, view, controller, and migration.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $this->askForName($input, $output);
        $name = ucfirst($name);
        $this->createModelFile($name);
        $this->createControllerFile($name);
        $this->createViewFile($name);
        $migrationquestion = $this->askForMigration($input, $output);
        $this->addRoute($name);
        
        $migrationquestion = strtolower($migrationquestion);

        if($migrationquestion == "si")
        {
            $this->createMigrationFile($name);
            $output->writeln("Model, view, controller, and migration for '$name' created successfully.");
        }else{
            $output->writeln("Model, view, controller for '$name' created successfully.");
        }

        return Command::SUCCESS;
    }

    protected function addRoute($name)
    {
        // Agregar las rutas generadas al archivo de rutas web.php
        $routeContent = "\n\n";
        //$routeContent .= "// Rutas generadas automáticamente por el comando make:all\n";
        $routeContent .= "\n";
        $routeContent .= "// Ruta para mostrar todos los registros\n";
        $routeContent .= "\$router->addRoute('GET', '/".lcfirst($name)."', 'App\Controllers\\{$name}Controller@index');\n";
        $routeContent .= "\n";
        //$routeContent .= "// Ruta para almacenar un nuevo registro\n";
        //$routeContent .= "\$router->addRoute('POST', '/".lcfirst($name)."', 'App\Controllers\\{$name}Controller@store');\n";
        //$routeContent .= "\n";

        // Leer el contenido actual del archivo de rutas
        $routePath = __DIR__ . '/../../routes/web.php';
        $currentRouteContent = file_get_contents($routePath);

        // Encontrar la posición de la declaración de retorno en el archivo de rutas
        $position = strrpos($currentRouteContent, 'return ');

        // Insertar las rutas generadas antes de la declaración de retorno
        $currentRouteContent = substr_replace($currentRouteContent, $routeContent, $position, 0);

        // Escribir el contenido actualizado de las rutas al archivo web.php
        file_put_contents($routePath, $currentRouteContent);


    }

    protected function askForMigration(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new Question('Quieres crear migracion? (Si/No)');
        $question->setValidator(function ($answer) {
            if (empty(trim($answer))) {
                throw new \RuntimeException('debes ingresar una respuesta.');
            }
            return ucfirst($answer);
        });

        return $helper->ask($input, $output, $question);
    }

    protected function askForName(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new Question('Enter the base name for model, view, controller, and migration: ');
        $question->setValidator(function ($answer) {
            if (empty(trim($answer))) {
                throw new \RuntimeException('The name cannot be empty.');
            }
            return ucfirst($answer);
        });

        return $helper->ask($input, $output, $question);
    }

    protected function createModelFile($name)
    {
        $content = "<?php\n\n";
        $content .= "namespace App\Models;\n\n";
        $content .= "use TetaFramework\Database\Model;\n\n";
        $content .= "class $name extends Model\n";
        $content .= "{\n";
        $content .= "    protected \$table = '".$name."s';\n";
        $content .= "    protected \$fillable = [];\n";
        $content .= "}\n";

        $path = __DIR__ . '/../../app/Models/' . $name . '.php';

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        file_put_contents($path, $content);
    }

    protected function createControllerFile($name)
    {
        

        $content = "<?php\n\n";
        $content .= "namespace App\Controllers;\n\n";
        $content .= "use App\Models\\$name;\n";
        $content .= "use TetaFramework\Http\Response;\n";
        $content .= "use TetaFramework\Http\Request;\n";
        $content .= "use TetaFramework\Http\RedirectResponse;\n";
        $content .= "use TetaFramework\Http\Session;\n";
        $content .= "use TetaFramework\View;\n";
        $content .= "\n";
        $content .= "protected \$ControllerName = '$name';";
        $content .= "{\n";
        $content .= "class {$name}Controller extends Controller\n";
        $content .= "{\n";
        $content .= "    public function index(Request \$request): Response\n";
        $content .= "    {\n";
        $content .= "        \$items = $name::all();\n";
        $content .= "        view::assign('lang', \$this->getLang()->getAllTranslate());\n";
        $content .= "        view::assign('uri', \$request->getPathInfo());\n";
        $content .= "        view::assign('locale', \$this->getLang()->getLocale());\n";
        $content .= "        \$content =  view::render('$name');\n";
        $content .= "        return new Response(\$content);\n";
        $content .= "    }\n\n";
        $content .= "}\n";
        
        
        
        $path = __DIR__ . '/../../app/Controllers/' . $name . 'Controller.php';

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        file_put_contents($path, $content);
    }

    protected function createViewFile($name)
    {
        $content = "<!-- View: $name -->\n";
        $content .= "<h1>$name</h1>\n";
        $content .= "<p>This is the $name view.</p>\n";

        $path = __DIR__ . '/../../views/' . strtolower($name) . '.php';

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        file_put_contents($path, $content);
    }

    protected function createMigrationFile($name)
    {
        $content = "<?php\n\n";
        $content .= "use TetaFramework\Database\Blueprint;\n";
        $content .= "use TetaFramework\Database\Migrations\Migration;\n";
        $content .= "use TetaFramework\Database\DatabaseManager;\n\n";
        $content .= "class Create" . $name . "Table extends Migration\n";
        $content .= "{\n";
        $content .= "    public function up()\n";
        $content .= "    {\n";
        $content .= "       try{\n";
        $content .= "            DatabaseManager::schema()->create('" . strtolower($name) . "s', function (Blueprint \$table) {\n";
        $content .= "            \$table->increments('id');\n";
        $content .= "            \$table->timestamps();\n";
        $content .= "        });\n";
        $content .= "       } catch (\Throwable \$th) {\n";
        $content .= "            throw \$th;";
        $content .= "       }\n";
        $content .= "    }\n\n";
        $content .= "    public function down()\n";
        $content .= "    {\n";
        $content .= "        DatabaseManager::schema()->dropIfExists('" . strtolower($name) . "s');\n";
        $content .= "    }\n";
        $content .= "}\n";

        $currentDateTime =  Date("YmdHis"); 

        $path = __DIR__ . '/../../database/migrations/' . $currentDateTime . '_create_' . strtolower($name) . '_table.php';

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        file_put_contents($path, $content);
    }
}
