<?php

namespace TetaFramework\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command
{
    protected static $defaultName = 'db:migrate';

    protected function configure()
    {
        $this->setDescription('Run database migrations.') 
        ->addOption('rollback', null, null, 'Rollback the last database migration');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        include_once 'bootstrap.php';
        $directory = 'database/migrations'; // Reemplaza '/ruta/a/tu/directorio' con la ruta real
        $migraciones = glob($directory . '/*.php');

        sort($migraciones);

        $rollback = $input->getOption('rollback');

        if ($rollback) {
            // Ejecutar las migraciones en orden inverso para rollback
            $migraciones = array_reverse($migraciones);
            $output->writeln('Rolling back migrations:');
        } else {
            $output->writeln('Running migrations:');
        }
        
        foreach ($migraciones as $migracion) {
            $partes = explode('_',$migracion);
            $size = count($partes);
            $buildClass = "";
            for($i = 1; $i < $size; $i++)
            {
                if(!str_contains($partes[$i],"."))
                {
                    $buildClass .= ucfirst($partes[$i]);
                }else{
                    $split = explode(".",$partes[$i]);
                    $buildClass .= ucfirst($split[0]);
                }
            }
            require_once $migracion;
            if(class_exists($buildClass))
            {
                $migracionFile = new $buildClass();

                try {
                    if ($rollback) {
                        if (method_exists($migracionFile, 'down')) {
                            $migracionFile->down();
                            $output->writeln("Rolled back: {$buildClass}");
                        } else {
                            $output->writeln("No down method found for: {$buildClass}");
                        }
                    } else {
                        if (method_exists($migracionFile, 'up')) {
                            $migracionFile->up();
                            $output->writeln("Migrated: {$buildClass}");
                        } else {
                            $output->writeln("No up method found for: {$buildClass}");
                        }
                    }
                } catch (\Throwable $th) {
                    //throw $th;
                    echo $th;
                }
            }
        }

        // Informar sobre el éxito de las migraciones
        // $output->writeln('Migrations executed successfully.');

        return Command::SUCCESS;
       
    }
}
