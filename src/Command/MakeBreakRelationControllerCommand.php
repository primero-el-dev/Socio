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
    name: 'make:break-relation-controller',
    description: 'Make controller for breaking user-user relation',
)]
class MakeBreakRelationControllerCommand extends Command
{
    public function __construct(private string $projectDir)
    {
        parent::__construct('make:break-relation-controller');
    }

    protected function configure(): void
    {
        $this
            ->addArgument('prefix', InputArgument::REQUIRED, 'Name prefix')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $prefix = $input->getArgument('prefix');

        if (file_exists($this->getPath($prefix))) {
            $io->error('Controller already exists');

            return Command::FAILURE;
        }

        $this->createEvent($prefix);

        $io->success('Controller created');

        return Command::SUCCESS;
    }

    private function createEvent(string $prefix): void
    {
        file_put_contents($this->getPath($prefix), $this->getTemplate($prefix));
    }

    private function getPath(string $prefix): string
    {
        return sprintf(
            '%s/src/Controller/Relation/%sController.php',
            $this->projectDir,
            $prefix
        );
    }

    private function getTemplate(string $prefix): string
    {
        return sprintf('<?php

namespace App\Controller\Relation;

use App\Entity\UserSubjectRelation;
use App\Controller\Relation\BreakUserUserRelationController;
use Symfony\Component\HttpFoundation\Response;
use App\Event\User\Relation\%sEvent;

class %sController extends BreakUserUserRelationController
{
    protected function getEventClass(): string
    {
        return %sEvent::class;
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
        return \'notification.success.%s\';
    }
}
',
            $prefix,
            $prefix,
            $prefix,
            lcfirst($prefix)
        );
    }
}
