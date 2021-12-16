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
    name: 'make:create-relation-controller',
    description: 'Make controller for creatring new user-user relation',
)]
class MakeCreateRelationControllerCommand extends Command
{
    public function __construct(private string $projectDir)
    {
        parent::__construct('make:create-relation-controller');
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

use App\Controller\Relation\MakeUserUserRelationController;
use App\Entity\UserSubjectRelation;
use App\Event\User\Relation\%sEvent;

class %sController extends MakeUserUserRelationController
{
    protected function getEventClass(): string
    {
        return %sEvent::class;
    }

    protected function getLoggedUserCreateRelations(): array
    {
        return [
            
        ];
    }

    protected function getSubjectUserCreateRelations(): array
    {
        return [
            
        ];
    }

    protected function getLoggedUserDeleteRelations(): array
    {
        return [
            UserSubjectRelation::%s,
        ];
    }

    protected function getSubjectUserDeleteRelations(): array
    {
        return [
            UserSubjectRelation::%s,
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
            StringUtil::camelCaseToSnakeCase($prefix, true),
            StringUtil::camelCaseToSnakeCase($prefix, true),
            lcfirst($prefix)
        );
    }
}
