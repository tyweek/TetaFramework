<?php

namespace TetaFramework\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ServeCommand extends Command
{
    protected static $defaultName = 'serve';

    protected function configure()
    {
        $this
            ->setDescription('Start the PHP built-in server.')
            ->addArgument('host', InputArgument::OPTIONAL, 'The host address to serve on.', '0.0.0.0')
            ->addArgument('port', InputArgument::OPTIONAL, 'The port to serve on.', '6100');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $host = $input->getArgument('host');
        $port = $input->getArgument('port');
        $documentRoot = __DIR__ . '/../../public';

        $output->writeln("Starting server on http://$host:$port");
        $output->writeln("Document root is $documentRoot");
        $output->writeln("Press Ctrl+C to stop the server");

        // $command = sprintf('php -S %s:%s -t %s', $host, $port, $documentRoot);
        $command = sprintf('php -S %s:%s -t "%s"', $host, $port, $documentRoot);
        passthru($command);

        return Command::SUCCESS;
    }
}
