<?php

namespace TetaFramework\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Application;

class HelpCommand extends Command
{
    protected static $defaultName = 'help';

    protected function configure()
    {
        $this->setDescription('Displays help information for all commands.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get the application instance
        $application = $this->getApplication();
        if (!$application) {
            $output->writeln('<error>Application instance not found.</error>');
            return Command::FAILURE;
        }

        // Get all commands
        $commands = $application->all();

        // Display each command's name and description
        foreach ($commands as $command) {
            $output->writeln(sprintf('<info>%s</info> - %s', $command->getName(), $command->getDescription()));
        }

        return Command::SUCCESS;
    }
}
