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
use Symfony\Component\Yaml\Yaml;

#[AsCommand(
    name: 'yaml:remove-key',
    description: 'Add a short description for your command',
)]
class YamlRemoveKeyCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('filePath', InputArgument::REQUIRED, 'File path')
            ->addArgument('keys', InputArgument::REQUIRED, "Yaml keys in format 'key1.key2.key3...'")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getArgument('filePath');
        $keys = $input->getArgument('keys');

        if (!is_readable($filePath)) {
            $io->error(sprintf('File "%s" doesn\'t exist.', $filePath));
            
            return Command::FAILURE;
        }

        $content = Yaml::parseFile($filePath);
        ArrayUtil::removeTreeKeys(explode('.', $keys), $content);
        $content = Yaml::dump($content, 10, 2, Yaml::DUMP_OBJECT);
        file_put_contents($filePath, $content);

        $io->success("Yaml key chain '$keys' doesn't exist anymore in file '$filePath'.");

        return Command::SUCCESS;
    }
}
