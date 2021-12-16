<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Util\StringUtil;

#[AsCommand(
    name: 'make:relation-action',
    description: 'Make events, event handlers and controller for a relation',
)]
class MakeRelationActionCommand extends Command
{
    private const CONTROLLER_SUBPATH = 'Controller/Relation';
    private const EVENT_SUBPATH = 'Event/User/Relation';
    private const EVENT_HANDLER_SUBPATH = 'Event/Handler/User/Relation';

    public function __construct(private string $projectDir)
    {
        parent::__construct('make:relation-action');
    }

    protected function configure(): void
    {
        $this
            ->addArgument('relation', InputArgument::REQUIRED, 'Relation name written with-dashes')
            // ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $relation = $input->getArgument('relation');

        $prefix = StringUtil::dashedToCamelCase($relation);

        $this->createMakeController($prefix);

        // if ($arg1) {
        //     $io->note(sprintf('You passed an argument: %s', $arg1));
        // }

        // if ($input->getOption('option1')) {
        //     // ...
        // }

        $io->success('Done');
        // $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }

    private function createBreakController(string $name): void
    {
        $path = $this->getPath(self::CONTROLLER_SUBPATH, 'Break'.$name, 'Controller');

        if (file_exists($path)) {
            die('BAD');
        }

        file_put_contents($path, $this->getMakeRelationControllerTemplate($name));
    }

    private function getPath(
        string $subDirectory, 
        string $name, 
        string $postfix = '', 
        string $extension = 'php'
    ): string
    {
        return sprintf(
            '%s/src/%s/%s%s.%s', 
            $this->projectDir,
            $subDirectory, 
            $name, 
            $postfix, 
            $extension
        );
    }

    private function getBreakRelationControllerTemplate(string $name): string
    {
        return sprintf('<?php

namespace App\\%s;

use App\\Controller\\Relation\\BreakUserUserRelationController;
use Symfony\\Component\\HttpFoundation\\Response;
use App\\Event\\User\\Relation\\Break%sEvent;

class Break%sController extends BreakUserUserRelationController
{
    protected function getEventClass(): string
    {
        return Break%sEvent::class;
    }

    protected function getLoggedUserDeleteRelations(): array
    {
        return [
            
        ];
    }

    protected function getSubjectUserDeleteRelations(): array
    {
        return [
            
        ];
    }
    
    protected function getResponseKey(): string
    {
        return \'\';
    }
}
',
            str_replace('/', '\\', self::CONTROLLER_SUBPATH),
            $name,
            $name,
            $name
        );
    }

    private function getPartialNamespaceFromPath(string $path): string
    {
        $parts = explode('/', $path);
        array_shift($parts);
        array_pop($parts);

        return implode('\\', $path);
    }

    private function getFileFromNamePath(string $path): string
    {
        $parts = explode('/', $path);
        $filename = array_pop($parts);

        return explode('.', $filename)[0];
    }

    private function getFilenamePrefix(string $filename, string $avoid): string
    {
        return preg_replace('/('.$avoid.')$/', '', $filename);
    }
}
