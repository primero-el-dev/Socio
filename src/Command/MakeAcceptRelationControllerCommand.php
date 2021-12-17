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
    name: 'make:accept-relation-controller',
    description: 'Make controller for creatring new user-user relation',
)]
class MakeAcceptRelationControllerCommand extends Command
{
    public function __construct(private string $projectDir)
    {
        parent::__construct('make:accept-relation-controller');
    }

    protected function configure(): void
    {
        $this
            ->addArgument('relation', InputArgument::REQUIRED, 'Relation name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $relation = $input->getArgument('relation');

        if (file_exists($this->getPath($relation))) {
            $io->error('Controller already exists');

            return Command::FAILURE;
        }

        $this->createEvent($relation);

        $io->success('Controller created');

        return Command::SUCCESS;
    }

    private function createEvent(string $relation): void
    {
        file_put_contents($this->getPath($relation), $this->getTemplate($relation));
    }

    private function getPath(string $relation): string
    {
        return sprintf(
            '%s/src/Controller/Relation/Accept%sRelationController.php',
            $this->projectDir,
            $relation
        );
    }

    private function getTemplate(string $relation): string
    {
        return sprintf('<?php

namespace App\Controller\Relation;

use App\Entity\User;
use App\Controller\Relation\MakeUserUserRelationController;
use App\Entity\UserSubjectRelation;
use Symfony\Component\HttpFoundation\Request;
use App\Event\User\Relation\Accept%sRelationEvent;

class Accept%sRelationController extends MakeUserUserRelationController
{
    protected function getEventClass(): string
    {
        return Accept%sRelationEvent::class;
    }

    protected function getLoggedUserCreateRelations(): array
    {
        return [
            
        ];
    }

    protected function getSubjectUserCreateRelations(): array
    {
        return [
            UserSubjectRelation::%s,
        ];
    }

    protected function getLoggedUserDeleteRelations(): array
    {
        return [
            
        ];
    }

    protected function getSubjectUserDeleteRelations(): array
    {
        return [
            UserSubjectRelation::REQUEST_%s,
        ];
    }
    
    protected function getResponseKey(): string
    {
        return \'notification.success.relation.accept%s\';
    }

    protected function additionalAction(User $user, Request $request): void
    {
        //
    }
}
',
            $relation,
            $relation,
            $relation,
            StringUtil::camelCaseToSnakeCase($relation, StringUtil::UPPERCASE),
            StringUtil::camelCaseToSnakeCase($relation, StringUtil::UPPERCASE),
            ucfirst($relation)
        );
    }
}
