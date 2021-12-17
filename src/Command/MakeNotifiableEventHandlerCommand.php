<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'make:notifiable-event-handler',
    description: 'Make notifiable relation action event handler',
)]
class MakeNotifiableEventHandlerCommand extends Command
{
    public function __construct(private string $projectDir)
    {
        parent::__construct('make:notifiable-event-handler');
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
            $io->error('Event handler already exists');

            return Command::FAILURE;
        }

        $this->createEventHandler($relation);

        $io->success('Event handler created');

        return Command::SUCCESS;
    }

    private function createEventHandler(string $relation): void
    {
        file_put_contents($this->getPath($relation), $this->getTemplate($relation));
    }

    private function getPath(string $relation): string
    {
        return sprintf(
            '%s/src/Event/Handler/User/Relation/%sRelationEventHandler.php',
            $this->projectDir,
            $relation
        );
    }

    private function getTemplate(string $relation): string
    {
        return sprintf('<?php

namespace App\Event\Handler\User\Relation;

use App\Event\Handler\Interface\NotifiableRelationActionEventHandlerInterface;
use App\Event\Handler\Trait\NotifiableRelationActionEventHandlerTrait;
use App\Event\User\Relation\%sRelationEvent;
use App\Event\Handler\User\Relation\UserUserRelationEventHandler;

class %sRelationEventHandler extends UserUserRelationEventHandler implements NotifiableRelationActionEventHandlerInterface
{
    use NotifiableRelationActionEventHandlerTrait;

    public function __invoke(%sRelationEvent $event): void
    {
        $this->handleNotifiableRelationActionEvent($event);
    }

    public function getSubjectKey(): string
    {
        return \'notification.info.relation.%s.subject\';
    }

    public function getContentKey(): string
    {
        return \'notification.info.relation.%s.content\';
    }
}',
            $relation,
            $relation,
            $relation,
            lcfirst($relation),
            lcfirst($relation)
        );
    }
}
