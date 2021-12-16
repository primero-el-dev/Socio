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
            ->addArgument('prefix', InputArgument::REQUIRED, 'Name prefix')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $prefix = $input->getArgument('prefix');

        if (file_exists($this->getPath($prefix))) {
            $io->error('Event already exists');

            return Command::FAILURE;
        }

        $this->createEvent($prefix);

        $io->success('Event created');

        return Command::SUCCESS;
    }

    private function createEvent(string $prefix): void
    {
        file_put_contents($this->getPath($prefix), $this->getTemplate($prefix));
    }

    private function getPath(string $prefix): string
    {
        return sprintf(
            '%s/src/Event/User/Relation/%sEvent.php',
            $this->projectDir,
            $prefix
        );
    }

    private function getTemplate(string $prefix): string
    {
        return sprintf('<?php

namespace App\Event\User\Relation;

use App\Entity\Notification;
use App\Event\Interface\NotifiableRelationActionEvent;
use App\Event\User\Relation\RelationActionEvent;

class %sEvent extends RelationActionEvent implements NotifiableRelationActionEvent
{
    public function getType(): string
    {
        return Notification::%s;
    }
}',
            $prefix,
            StringUtil::camelCaseToSnakeCase($prefix, true)
        );
    }
}
