<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArrayInput;

#[AsCommand(
    name: 'make:notifiable-event-translations',
    description: 'Add a short description for your command',
)]
class MakeNotifiableEventTranslationsCommand extends Command
{
    private const FILE_PATH = 'translations/messages.en.yaml';

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

        if (!is_readable(static::FILE_PATH)) {
            $io->error(sprintf(
                "File \"%s\" doesn't exists", 
                static::FILE_PATH
            ));

            return Command::FAILURE;
        }

        $this->updateYamlKeys($relation, $output);

        $io->success('Configuration updated');

        return Command::SUCCESS;
    }

    private function updateYamlKeys(string $relation, OutputInterface $output): void
    {
        foreach (['request', 'accept', 'break'] as $action) {
            $this->updateYamlKey(
                ['notification', 'info', 'relation', $action.ucfirst($relation), 'subject'], 
                $this->{'get'.ucfirst($action).'RelationSubject'}($relation),
                $output
            );
            $this->updateYamlKey(
                ['notification', 'info', 'relation', $action.ucfirst($relation), 'content'], 
                $this->{'get'.ucfirst($action).'RelationMessage'}($relation),
                $output
            );
            $this->updateYamlKey(
                ['notification', 'success', 'relation', $action.ucfirst($relation)],
                $this->{'get'.ucfirst($action).'RelationSuccessMessage'}($relation),
                $output
            );
        }
    }

    private function updateYamlKey(array $keys, $value, OutputInterface $output): void
    {
        $command = $this->getApplication()->find('yaml:update-key');

        $command->run(new ArrayInput([
            'filePath' => static::FILE_PATH,
            'keys' => implode('.', $keys),
            'value' => $value,
        ]), $output);
    }

    private function getRequestRelationSubject(string $relation): string
    {
        return 'User "%s" send you ' . strtolower($relation) . ' relation request';
    }

    private function getAcceptRelationSubject(string $relation): string
    {
        return ucfirst($relation) . ' relation accepted';
    }

    private function getBreakRelationSubject(string $relation): string
    {
        return ucfirst($relation) . ' relation broken';
    }

    private function getRequestRelationMessage(string $relation): string
    {
        return 'User "%s" send you ' . strtolower($relation) . ' relation request.';
    }

    private function getAcceptRelationMessage(string $relation): string
    {
        return 'User "%s" is now Your ' . strtolower($relation) . '.';
    }

    private function getBreakRelationMessage(string $relation): string
    {
        return 'User "%s" is now not Your ' . strtolower($relation) . '.';
    }

    private function getRequestRelationSuccessMessage(string $relation): string
    {
        return 'Your ' . strtolower($relation) . ' request has been send.';
    }

    private function getAcceptRelationSuccessMessage(string $relation): string
    {
        return ucfirst($relation) . ' request was accepted.';
    }

    private function getBreakRelationSuccessMessage(string $relation): string
    {
        return "You've broken a " . strtolower($relation) . " relationship successfully.";
    }
}
