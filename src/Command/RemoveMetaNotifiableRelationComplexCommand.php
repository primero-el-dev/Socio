<?php

namespace App\Command;

use App\Util\ArrayUtil;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArrayInput;

#[AsCommand(
    name: 'remove:meta:notifiable-relation-complex',
    description: 'Desc',
)]
class RemoveMetaNotifiableRelationComplexCommand extends Command
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

        $this->removeFiles($this->getPaths($relation), $output);
        $this->updateYamlFiles($this->getYamlKeys($relation), $output);

        $io->success('All done.');

        return Command::SUCCESS;
    }

    private function getPaths(string $relation): array
    {
        $actions = ['Break', 'Accept', 'Request'];
        $paths = [
            'src/Controller/Relation/%s%sRelationController.php',
            'src/Controller/Relation/%sRelationController.php',
            'src/EventHandler/User/Relation/%s%sRelationEventHandler.php',
            'src/Event/User/Relation/%s%sRelationEvent.php',
        ];

        $paths = array_map(
            fn($path) => $this->getRelationActionPaths($relation, $actions, $path),
            $paths
        );
        $paths[] = sprintf('src/Security/Voter/Relation/%sRelationVoter.php', $relation);

        return ArrayUtil::flatten($paths);
    }

    private function getYamlKeys(string $relation): array
    {
        $actions = ['break', 'accept', 'request'];

        return array_map(fn($action) => $this->getYamlKeyChain($action, $relation), $actions);
    }

    private function getYamlKeyChain(string $action, string $relation): array
    {
        return [
            'App\\Entity\\User', 
            'itemOperations', 
            sprintf('%s_%s_relation', strtolower($action), strtolower($relation)),
        ];
    }

    private function getRelationActionPaths(string $relation, array $actions, string $path): array
    {
        return array_map(fn ($action) => sprintf($path, $action, $relation), $actions);
    }

    private function removeFiles(array $paths, OutputInterface $output): void
    {
        foreach ($paths as $path) {
            $this->removeFile($path, $output);
        }
    }

    private function removeFile(string $path, OutputInterface $output): void
    {
        $command = $this->getApplication()->find('remove:file');
        $command->run(new ArrayInput(['filePath' => $path]), $output);
    }

    private function updateYamlFiles(array $keys, OutputInterface $output): void
    {
        foreach ($keys as $keyChain) {
            $this->updateYamlFile($keyChain, $output);
        }
    }

    private function updateYamlFile(array $keys, OutputInterface $output): void
    {
        $command = $this->getApplication()->find('yaml:remove-key');
        $command->run(new ArrayInput([
            'filePath' => 'config/api_platform/user.yaml',
            'keys' => implode('.', $keys),
        ]), $output);
    }

    private function removeTranslations(string $relation, OutputInterface $output): void
    {
        $command = $this->getApplication()->find('remove:notifiable-event-translations');
        $command->run(new ArrayInput(['relation' => $relation]), $output);
    }
}
