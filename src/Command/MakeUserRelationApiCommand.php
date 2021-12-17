<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use App\Util\StringUtil;

#[AsCommand(
    name: 'make:user-relation-api',
    description: 'Add a short description for your command',
)]
class MakeUserRelationApiCommand extends Command
{
    private const CONFIG_PATH = '/config/api_platform/user.yaml';

    public function __construct(private string $projectDir)
    {
        parent::__construct('make:user-relation-api');
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

        $content = Yaml::parseFile($this->projectDir . self::CONFIG_PATH);

        if ($this->routeExists($content, $relation)) {
            $io->error(sprintf(
                'Route "%s" already exists', 
                $this->getRouteName($relation)
            ));

            return Command::FAILURE;
        }

        $this->createApiRoute($content, $relation);

        $content = Yaml::dump($content, 10, 2, Yaml::DUMP_OBJECT);
        file_put_contents($this->projectDir . self::CONFIG_PATH, $content);

        $io->success('Configuration updated');

        return Command::SUCCESS;
    }

    private function routeExists(array $content, string $relation): bool
    {
        $route = $this->getRouteName($relation);

        return (isset($content['App\\Entity\\User']['itemOperations'][$route]));
    }

    private function getRouteName(string $relation): string
    {
        return sprintf(
            '%s_relation', 
            StringUtil::camelCaseToSnakeCase($relation, StringUtil::LOWERCASE)
        );
    }

    private function createApiRoute(array &$content, string $relation): void
    {
        $route = $this->getRouteName($relation);
        $content['App\\Entity\\User']['itemOperations'][$route] = [
            'security' => sprintf(
                'is_granted("%s_RELATION", object)',
                StringUtil::camelCaseToSnakeCase($relation, StringUtil::UPPERCASE)
            ),
            'path' => sprintf(
                '/users/{id}/%s-relation', 
                StringUtil::camelCaseToDashed($relation, StringUtil::LOWERCASE)
            ),
            'method' => 'POST',
            'controller' => sprintf(
                'App\Controller\Relation\%sRelationController', 
                $relation
            ),
            'requirements' => [
                'id' => '\d+'
            ],
        ];
    }
}
