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
    name: 'remove:notifiable-event-translations',
    description: 'Add a short description for your command',
)]
class RemoveNotifiableEventTranslationsCommand extends Command
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
            $this->removeYamlKey(
                ['notification', 'info', 'relation', $action.$relation], 
                $output
            );
            $this->removeYamlKey(
                ['notification', 'success', 'relation', $action.$relation], 
                $output
            );
        }
    }

    private function removeYamlKey(array $keys, OutputInterface $output): void
    {
        $command = $this->getApplication()->find('yaml:remove-key');

        $command->run(new ArrayInput([
            'filePath' => static::FILE_PATH,
            'keys' => implode('.', $keys),
        ]), $output);
    }
}
