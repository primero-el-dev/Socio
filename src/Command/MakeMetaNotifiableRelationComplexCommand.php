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
    description: 'Add a short description for your command',
)]
class MakeMetaNotifiableRelationComplexCommand extends Command
{
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

        $this->createEvents($prefix, $output);
        $this->createEventHandlers($prefix, $output);
        $this->createBreakRelationControllers($prefix, $output);
        $this->createMakeRelationControllers($prefix, $output);

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

    private function createBreakRelationControllers(string $name, OutputInterface $output): void
    {
        $this->createElements($name, $output, 'make:break-relation-controller', ['Break']);
    }

    private function createMakeRelationControllers(string $name, OutputInterface $output): void
    {
        $this->createElements($name, $output, 'make:create-relation-controller', 
            ['Accept', 'Request']);
    }

    private function createElements(
        string $name, 
        OutputInterface $output,
        string $commandName,
        array $prefixes = ['Break', 'Accept', 'Request']
    ): void
    {
        $command = $this->getApplication()->find($commandName);

        foreach ($prefixes as $prefix) {
            $command->run(new ArrayInput(['prefix' => $prefix.$name]), $output);
        }
    }
}
