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
    name: 'make:relation-voter',
    description: 'Make a voter for relation',
)]
class MakeRelationVoterCommand extends Command
{
    public function __construct(private string $projectDir)
    {
        parent::__construct('make:relation-voter');
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

        if (file_exists($this->getPath($relation))) {
            $io->error('Voter already exists');

            return Command::FAILURE;
        }

        $this->createVoter($relation);

        $io->success('Voter created');

        return Command::SUCCESS;
    }

    private function createVoter(string $relation): void
    {
        file_put_contents($this->getPath($relation), $this->getTemplate($relation));
    }

    private function getPath(string $relation): string
    {
        return sprintf(
            '%s/src/Security/Voter/Relation/%sRelationVoter.php',
            $this->projectDir,
            $relation
        );
    }

    private function getTemplate(string $relation): string
    {
        return sprintf('<?php

namespace App\Security\Voter\Relation;

use App\Entity\UserSubjectRelation;
use App\Security\Voter\Relation\RelationVoter;

class %sRelationVoter extends RelationVoter
{
    protected function getRoles(): array
    {
        return [
            \'request_relation\' => \'REQUEST_%s_RELATION\',
            \'accept_relation\' => \'ACCEPT_%s_RELATION\',
            \'break_relation\' => \'BREAK_%s_RELATION\',
        ];
    }

    protected function getRelationRequest(): string
    {
        return UserSubjectRelation::REQUEST_%s;
    }

    protected function getRealizedRelation(): string
    {
        return UserSubjectRelation::%s;
    }
}',
            $relation,
            StringUtil::camelCaseToSnakeCase($relation, StringUtil::UPPERCASE),
            StringUtil::camelCaseToSnakeCase($relation, StringUtil::UPPERCASE),
            StringUtil::camelCaseToSnakeCase($relation, StringUtil::UPPERCASE),
            StringUtil::camelCaseToSnakeCase($relation, StringUtil::UPPERCASE),
            StringUtil::camelCaseToSnakeCase($relation, StringUtil::UPPERCASE)
        );
    }
}
