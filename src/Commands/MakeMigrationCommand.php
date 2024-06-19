<?php

namespace TetaFramework\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class MakeMigrationCommand extends Command
{
    protected static $defaultName = 'make:migration';

    protected function configure()
    {
        $this->setDescription('Create a new migration file.')
             ->setHelp('This command allows you to create a new migration file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
       
        $migrationName = $this->askForMigrationName($input, $output);
        $this->createMigrationFile($migrationName);
        $output->writeln("Migration  '$migrationName' created successfully.");
        return Command::SUCCESS;
    }

    protected function askForMigrationName(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new Question('Enter the name of the migration: ');
        $question->setValidator(function ($answer) {
            if (empty(trim($answer))) {
                throw new \RuntimeException('The migration name cannot be empty.');
            }
            return $answer;
        });

        return $helper->ask($input, $output, $question);
    }

    protected function createMigrationFile($name)
    {
        $content = "<?php\n\n";
        $content .= "use TetaFramework\Database\DatabaseManager;\n";
        $content .= "use TetaFramework\Database\Blueprint;\n";
        $content .= "use TetaFramework\Database\Migrations\Migration;\n\n";
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
        $content .= "      }\n";
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