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
        $content .= "use Symfony\Component\HttpFoundation\Response;\n";
        $content .= "use TetaFramework\View;\n\n";
        $content .= "class $controllerName\n";
        $content .= "{\n";
        $content .= "    public function index()\n";
        $content .= "    {\n";
        $content .= "        // Aquí puedes agregar la lógica de tu controlador\n";
        $content .= "        return new Response(View::render('$controllerName',[]));\n";
        $content .= "    }\n";
        $content .= "}\n";

        // Ruta donde se guardará el archivo del controlador
        $path = __DIR__ . '/../../app/Controllers/' . $controllerName . 'Controller.php';

        // Escribir el contenido en el archivo
        file_put_contents($path, $content);

        // Mostrar un mensaje indicando que el archivo se ha creado
        echo "Controller file for '$controllerName' created successfully at '$path'.";
    }
}
