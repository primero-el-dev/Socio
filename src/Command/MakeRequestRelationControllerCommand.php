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
    name: 'make:request-relation-controller',
    description: 'Make controller for creatring new user-user relation',
)]
class MakeRequestRelationControllerCommand extends Command
{
    public function __construct(private string $projectDir)
    {
        parent::__construct('make:request-relation-controller');
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
            '%s/src/Controller/Relation/Request%sRelationController.php',
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
use App\Event\User\Relation\Request%sRelationEvent;

class Request%sRelationController extends MakeUserUserRelationController
{
    protected function getEventClass(): string
    {
        return Request%sRelationEvent::class;
    }

    protected function getLoggedUserCreateRelations(User $user, User $subject): array
    {
        return [
            UserSubjectRelation::REQUEST_%s,
        ];
    }

    protected function getSubjectUserCreateRelations(User $user, User $subject): array
    {
        return [
            
        ];
    }

    protected function getLoggedUserDeleteRelations(User $user, User $subject): array
    {
        return [
            
        ];
    }

    protected function getSubjectUserDeleteRelations(User $user, User $subject): array
    {
        return [
            
        ];
    }
    
    protected function getResponseKey(): string
    {
        return \'notification.success.relation.request%s\';
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
            ucfirst($relation)
        );
    }
}

