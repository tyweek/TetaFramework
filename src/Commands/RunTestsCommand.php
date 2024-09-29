<?php

namespace TetaFramework\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunTestsCommand extends Command
{
    protected static $defaultName = 'run-tests';

    protected function configure()
    {
        $this->setDescription('Run tests using PHPUnit.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Ejecutar el comando `composer test`
        exec('composer test', $output1, $returnCode);

        // Verificar el código de retorno del comando ejecutado
        if ($returnCode === 0) {
             $output->writeln('<info>Tests executed successfully.</info>');
             $output->writeln($output1);
            return Command::SUCCESS;
        } else {
             $output->writeln('<error>Error executing tests.</error>');
            return Command::FAILURE;
        }
    }
}
