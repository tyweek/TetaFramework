<?php

namespace TetaFramework\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class MakeControllerCommand extends Command
{
    protected static $defaultName = 'make:controller';

    protected function configure()
    {
        $this->setDescription('Create a new controller.')
             ->setHelp('This command allows you to create a new controller.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $controllerName = $this->askForControllerName($input, $output);
        $this->createControllerFile($controllerName);
        $output->writeln("Controller '$controllerName' created successfully.");

        return Command::SUCCESS;
    }

    protected function askForControllerName(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new Question('Enter the name of the controller: ');
        $question->setValidator(function ($answer) {
            if (empty(trim($answer))) {
                throw new \RuntimeException('The controller name cannot be empty.');
            }
            return $answer;
        });

        return $helper->ask($input, $output, $question);
    }

    protected function createControllerFile($controllerName)
    {
        $controllerName = ucfirst($controllerName);
        // Definir el contenido del archivo PHP del controlador
        $content = "<?php\n\n";
        $content .= "namespace App\Controllers;\n\n";
        $content .= "use App\Models\\$controllerName;\n";
        $content .= "use TetaFramework\Http\Response;\n";
        $content .= "use TetaFramework\Http\Request;\n";
        $content .= "use TetaFramework\Http\RedirectResponse;\n";
        $content .= "use TetaFramework\Http\Session;\n";
        $content .= "use TetaFramework\View;\n";
        $content .= "\n";
        $content .= "class {$controllerName}Controller extends Controller\n";
        $content .= "{\n";
        $content .= "    public function index(Request \$request): Response\n";
        $content .= "    {\n";
        $content .= "        \$items = $controllerName::all();\n";
        $content .= "        view::assign('lang', \$this->getLang()->getAllTranslate());\n";
        $content .= "        view::assign('uri', \$request->getPathInfo());\n";
        $content .= "        view::assign('locale', \$this->getLang()->getLocale());\n";
        $content .= "        \$content =  view::render('$controllerName');\n";
        $content .= "        return new Response(\$content);\n";
        $content .= "    }\n\n";
        $content .= "}\n";

        // Ruta donde se guardará el archivo del controlador
        $path = __DIR__ . '/../../app/Controllers/' . $controllerName . 'Controller.php';

        // Escribir el contenido en el archivo
        file_put_contents($path, $content);

        // Mostrar un mensaje indicando que el archivo se ha creado
        echo "Controller file for '$controllerName' created successfully at '$path'.";
    }
}
