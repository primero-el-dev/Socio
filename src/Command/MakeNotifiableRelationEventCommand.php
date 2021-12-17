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
    name: 'make:notifiable-event',
    description: 'Make notifiable relation action event',
)]
class MakeNotifiableRelationEventCommand extends Command
{
    public function __construct(private string $projectDir)
    {
        parent::__construct('make:notifiable-event');
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
            $io->error('Event already exists');

            return Command::FAILURE;
        }

        $this->createEvent($relation);

        $io->success('Event created');

        return Command::SUCCESS;
    }

    private function createEvent(string $relation): void
    {
        file_put_contents($this->getPath($relation), $this->getTemplate($relation));
    }

    private function getPath(string $relation): string
    {
        return sprintf(
            '%s/src/Event/User/Relation/%sRelationEvent.php',
            $this->projectDir,
            $relation
        );
    }

    private function getTemplate(string $relation): string
    {
        return sprintf('<?php

namespace App\Event\User\Relation;

use App\Entity\Notification;
use App\Event\Interface\NotifiableRelationActionEvent;
use App\Event\User\Relation\RelationActionEvent;

class %sRelationEvent extends RelationActionEvent implements NotifiableRelationActionEvent
{
    public function getType(): string
    {
        return Notification::%s_RELATION;
    }
}',
            $relation,
            StringUtil::camelCaseToSnakeCase($relation, true)
        );
    }
}
