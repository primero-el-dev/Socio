<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'remove:file',
    description: 'Remove file.',
)]
class RemoveFileCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('filePath', InputArgument::REQUIRED, 'File path')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getArgument('filePath');
        $filesystem = new Filesystem();
        
        if ($filesystem->exists([$filePath])) {
            $filesystem->remove([$filePath]);
        }

        $io->success("File '$filePath' doesn't exist.");

        return Command::SUCCESS;
    }
}
