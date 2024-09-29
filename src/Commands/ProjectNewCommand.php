<?php

namespace TetaFramework\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class ProjectNewCommand extends Command
{
    protected static $defaultName = 'project:new';

    protected function configure()
    {
        $this->setDescription('Crea modelo,vista y controlador inicial, asi como las rutas y otros archivos necesarios')
             ->setHelp('This command allows you to create a new model, view, controller, and migration.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // $name = $this->askForName($input, $output);
        // $name = ucfirst($name);


        //crear controladores
        $this->createControllerFile("","-base");
        $this->createControllerFile("Auth","-auth");
        $this->createControllerFile("Panel","-panel");

        //crear modelos
        $this->createModelFile("Roles","-roles");
        $this->createModelFile("User","-user");
        $this->createModelFile("AuthSession","-session");

        //Crear Vistas
        $this->createViewFile("Login","-login");
        $this->createViewFile("Panel","-panel");

        //Crear Resources
        $this->createResourceFile("login","-login","css");
        $this->createResourceFile("panel","-panel","css");

        //Crear Migraciones
        $this->createMigrationFile("Roles",1,"-roles");
        $this->createMigrationFile("Users",2,"-user");
        $this->createMigrationFile("sessions",3,"-session");

        //Crear Routes
        $this->addRoute();        


        $output->writeln("Base de TetaFramework creada!!");

        return Command::SUCCESS;
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

    protected function createFromFile($name,$from,$to,$type)
    {

        $pathTemplate = __DIR__ . '/../../src/project/initial/'.$type.'/';
        $filenameImport = $pathTemplate.$from;
        $filenameExport = $to;
        
        $currentRouteContent = file_get_contents($filenameImport);
        
        if (!file_exists(dirname($filenameExport))) {
            mkdir(dirname($filenameExport), 0777, true);
        }
        
        file_put_contents($filenameExport, $currentRouteContent);
    }
    protected function addRoute()
    {
        $path = __DIR__ . '/../../routes/web.php';
        $this->createFromFile("","routes-web.initial.php",$path,"routes");
    }
    protected function createModelFile($name,$prefix = "")
    {
        $path = __DIR__ . '/../../app/Models/' . $name . '.php';
        $this->createFromFile($name,"model.initial$prefix.php",$path,"models");
    }

    protected function createControllerFile($name,$prefix = "")
    {
        $path = __DIR__ . '/../../app/Controllers/' . $name . 'Controller.php';
        $this->createFromFile($name,"controller.initial$prefix.php",$path,"controllers");
    }

    protected function createViewFile($name,$prefix = "")
    {
        $path = __DIR__ . '/../../views/' . strtolower($name) . '.php';
        $this->createFromFile($name,"view.initial$prefix.php",$path,"views");
    }

    protected function createMigrationFile($name,$seconds = 0,$prefix = "")
    {
        $currentDateTime =  Date("YmdHis",time() + $seconds); 
        $path = __DIR__ . '/../../database/migrations/' . $currentDateTime . '_create_' . strtolower($name) . '_table.php';
        $this->createFromFile($name,"migration.initial$prefix.php",$path,"migrations");
    }

    protected function createResourceFile($name,$prefix = "",$type = "")
    {
        $extra = "";
        if(!empty($type))
            $extra = "/";
        $extension = ".php";

        if($type == "css")
            $extension = ".css";
        
        if($type == "js")
        $extension = ".js";

        $path = __DIR__ . '/../../public/assets/'.$type.$extra . strtolower($name) . $extension;
        $this->createFromFile($name,"resources.$type.initial$prefix.php",$path,"resources");
    }
}
