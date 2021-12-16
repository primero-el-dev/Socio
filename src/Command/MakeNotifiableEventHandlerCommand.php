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
            ->addArgument('prefix', InputArgument::REQUIRED, 'Name prefix')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $prefix = $input->getArgument('prefix');

        if (file_exists($this->getPath($prefix))) {
            $io->error('Event handler already exists');

            return Command::FAILURE;
        }

        $this->createEventHandler($prefix);

        $io->success('Event handler created');

        return Command::SUCCESS;
    }

    private function createEventHandler(string $prefix): void
    {
        file_put_contents($this->getPath($prefix), $this->getTemplate($prefix));
    }

    private function getPath(string $prefix): string
    {
        return sprintf(
            '%s/src/Event/Handler/User/Relation/%sEventHandler.php',
            $this->projectDir,
            $prefix
        );
    }

    private function getTemplate(string $prefix): string
    {
        return sprintf('<?php

namespace App\Event\Handler\User\Relation;

use App\Event\Handler\Interface\NotifiableRelationActionEventHandlerInterface;
use App\Event\Handler\Trait\NotifiableRelationActionEventHandlerTrait;
use App\Event\User\Relation\%sEvent;

class %sEventHandler implements NotifiableRelationActionEventHandlerInterface
{
    use NotifiableRelationActionEventHandlerTrait;

    public function __invoke(AcceptFriendshipEvent $event): void
    {
        $this->handleNotifiableRelationActionEvent($event);
    }

    public function getSubjectKey(): string
    {
        return \'notification.info.%s.subject\';
    }

    public function getContentKey(): string
    {
        return \'notification.info.%s.content\';
    }
}',
            $prefix,
            $prefix,
            lcfirst($prefix),
            lcfirst($prefix)
        );
    }
}
