<?php

namespace TetaFramework\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class MakeViewCommand extends Command
{
    protected static $defaultName = 'make:view';

    protected function configure()
    {
        $this->setDescription('Create a new view.')
             ->setHelp('This command allows you to create a new view.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $viewName = $this->askForViewName($input, $output);
        $this->createViewFile($viewName);
        $output->writeln("View '$viewName' created successfully.");

        return Command::SUCCESS;
    }

    protected function askForViewName(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new Question('Enter the name of the view: ');
        $question->setValidator(function ($answer) {
            if (empty(trim($answer))) {
                throw new \RuntimeException('The view name cannot be empty.');
            }
            return strtolower($answer); // Convertir a minúsculas
        });

        return $helper->ask($input, $output, $question);
    }

    protected function createViewFile($viewName)
    {
        $viewName = ucfirst($viewName);
        // Definir el contenido del archivo PHP de la vista
        $content = "<!-- View: $viewName -->\n";
        $content .= "<h1>$viewName</h1>\n";
        $content .= "<p>This is the $viewName view.</p>\n";

        // Ruta donde se guardará el archivo de la vista
        $path = __DIR__ . '/../../views/' . $viewName . '.php';

        // Crear el directorio si no existe
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        // Escribir el contenido en el archivo
        file_put_contents($path, $content);

        // Mostrar un mensaje indicando que el archivo se ha creado
        echo "View file for '$viewName' created successfully at '$path'.";
    }
}
