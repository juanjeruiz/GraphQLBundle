<?php

declare(strict_types=1);

namespace Overblog\GraphQLBundle\Command;

use GraphQL\Error\InvariantViolation;
use GraphQL\Error\Warning;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class ValidateCommand extends Command
{
    use RequestExecutorLazyLoaderTrait;

    protected function configure(): void
    {
        $this
            ->setName('graphql:validate')
            ->setDescription('Validate schema')
            ->addOption(
                'schema',
                null,
                InputOption::VALUE_OPTIONAL,
                'The schema name to validate.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        Warning::suppress(true);

        $schemaName = $input->getOption('schema');
        $schema = $this->getRequestExecutor()->getSchema($schemaName);

        try {
            $schema->assertValid();
        } catch (InvariantViolation $e) {
            $output->writeln('<comment>'.$e->getMessage().'</comment>');

            return;
        }
        $output->writeln('<info>No error</info>');
    }
}
