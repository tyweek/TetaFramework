<?php

namespace TetaFramework\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class MakeModelCommand extends Command
{
    protected static $defaultName = 'make:model';

    protected function configure()
    {
        $this->setDescription('Create a new model.')
             ->setHelp('This command allows you to create a new model.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $modelName = $this->askForModelName($input, $output);
        $tableName = $this->askForTableName($input, $output);
        $this->createModelFile($modelName,$tableName);
        $output->writeln("Model '$modelName' created successfully.");

        return Command::SUCCESS;
    }

    protected function askForModelName(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new Question('Enter the name of the model: ');
        $question->setValidator(function ($answer) {
            if (empty(trim($answer))) {
                throw new \RuntimeException('The model name cannot be empty.');
            }
            return $answer;
        });
        return $helper->ask($input, $output, $question);
    }
    protected function askForTableName(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $questionTable = new Question('Enter table name for model:');
        $questionTable->setValidator(function ($answer) {
            if (empty(trim($answer))) {
                throw new \RuntimeException('The table name cannot be empty.');
            }
            return $answer;
        });
        return $helper->ask($input, $output, $questionTable);
    }
    protected function createModelFile($modelName,$tableName)
    {
        $modelName = ucfirst($modelName);
        // Definir el contenido del archivo PHP del modelo
        $content = "<?php\n\n";
        $content .= "namespace App\Models;\n\n";
        $content .= "use TetaFramework\Database\Model;\n\n";
        $content .= "class $modelName extends Model\n";
        $content .= "{\n";
        $content .= "    protected \$table = '$tableName';\n";
        $content .= "    protected \$fillable = [];\n";
        $content .= "}\n";

        // Ruta donde se guardará el archivo del modelo
        $path = __DIR__ . '/../../app/Models/' . $modelName. '.php';

        // Escribir el contenido en el archivo
        file_put_contents($path, $content);

        // Mostrar un mensaje indicando que el archivo se ha creado
        echo "Model file for '$modelName' created successfully at '$path'.";
    }
}
