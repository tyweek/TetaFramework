<?php

namespace TetaFramework\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class MakeRouteCommand extends Command
{
    protected static $defaultName = 'make:route';

    protected function configure()
    {
        $this->setDescription('Crea modelo,vista y controlador inicial, asi como las rutas y otros archivos necesarios')
             ->setHelp('This command allows you to create a new model, view, controller, and migration.');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $route = $this->askForName($input, $output,"Ingrese Ruta:");
        $route = ucfirst($route);

        $controller = $this->askForName($input, $output,"Ingrese Controlador:");

        $function = $this->askForName($input, $output,"Ingrese Funcion:");

        $this->addRoute($route,$controller,$function);

        $output->writeln("Se creo la ruta ".$route." correctamente!");

        return Command::SUCCESS;
    }

    protected function createNewRoute($route,$ctrl,$fnc,$filenameImport)
    {

        $routeLine = '$router->addRoute'."('GET', '".$route."', 'App\Controllers\\".$ctrl."@".$fnc."');";

        $content = file_get_contents($filenameImport);

        $content = str_replace('return $router;',"",$content);

        $data = $routeLine.PHP_EOL;
        $fp = fopen($filenameImport, 'w');
        fwrite($fp, $content);
        fwrite($fp, $routeLine);
        fwrite($fp, "\n");
        fwrite($fp, "\n".'return $router;');
        fclose($fp);
    }
    protected function addRoute($route,$ctrl,$fnc)
    {
        $path = __DIR__ . '/../../routes/web.php';
        $this->createNewRoute($route,$ctrl,$fnc,$path);
    }

    protected function askForName(InputInterface $input, OutputInterface $output, $text)
    {
        $helper = $this->getHelper('question');
        $question = new Question($text);
        $question->setValidator(function ($answer) {
            if (empty(trim($answer))) {
                throw new \RuntimeException('The value cannot be empty.');
            }
            return ucfirst($answer);
        });

        return $helper->ask($input, $output, $question);
    }


}
