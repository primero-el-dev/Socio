<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'make:meta:notifiable-relation-complex',
    description: 'Make notifiable relation events, handlers and controllers',
)]
class MakeMetaNotifiableRelationComplexCommand extends Command
{
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

        $this->createEvents($relation, $output);
        $this->createEventHandlers($relation, $output);
        $this->createControllers($relation, $output);
        $this->createRelationVoter($relation, $output);
        $this->updateApiRoutes($relation, $output);

        $io->success('Everything done');

        return Command::SUCCESS;
    }

    private function createEvents(string $name, OutputInterface $output): void
    {
        $this->createElements($name, $output, 'make:notifiable-event');
    }

    private function createEventHandlers(string $name, OutputInterface $output): void
    {
        $this->createElements($name, $output, 'make:notifiable-event-handler');
    }

    private function createControllers(string $name, OutputInterface $output): void
    {
        foreach (['break', 'accept', 'request'] as $action) {
            $command = $this->getApplication()->find('make:'.$action.'-relation-controller');
            $command->run(new ArrayInput(['relation' => $name]), $output);
        }
    }

    private function createMakeRelationControllers(string $name, OutputInterface $output): void
    {
        $this->createElements($name, $output, 'make:create-relation-controller', 
            ['Accept', 'Request']);
    }

    private function createRelationVoter(string $name, OutputInterface $output): void
    {
        $command = $this->getApplication()->find('make:relation-voter');

        $command->run(new ArrayInput(['relation' => $name]), $output);
    }

    private function updateApiRoutes(string $name, OutputInterface $output): void
    {
        $this->createElements($name, $output, 'make:user-relation-api', 
            ['Break', 'Accept', 'Request']);
    }

    private function createElements(
        string $relation, 
        OutputInterface $output,
        string $commandName,
        array $prefixes = ['Break', 'Accept', 'Request']
    ): void
    {
        $command = $this->getApplication()->find($commandName);

        foreach ($prefixes as $prefix) {
            $command->run(new ArrayInput(['relation' => $prefix.$relation]), $output);
        }
    }
}
