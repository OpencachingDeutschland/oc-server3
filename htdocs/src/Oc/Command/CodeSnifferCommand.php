<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

namespace Oc\Command;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Class CodeSnifferCommand
 *
 * @package Oc\Command
 */
class CodeSnifferCommand extends AbstractCommand
{
    const COMMAND_NAME = 'code:sniff';

    const OPTION_DRY_RUN = 'dry';
    const OPTION_FIX = 'fix';
    const OPTION_XML = 'xml';
    const OPTION_DIR = 'dir';

    /**
     * Configures the command.
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Wrapper for phpcs');

        $this->addOption(self::OPTION_DRY_RUN, 't', InputOption::VALUE_NONE, 'Dry run the command');
        $this->addOption(self::OPTION_FIX, 'f', InputOption::VALUE_NONE, 'Fix errors that can be fixed automatically');
        $this->addOption(self::OPTION_XML, 'x', InputOption::VALUE_NONE, 'Write to checkstyle xml file');
        $this->addOption(self::OPTION_DIR, 'd', InputOption::VALUE_REQUIRED, 'Specify directory or file to check');
    }

    /**
     * Executes the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     *
     * @throws RuntimeException
     * @throws LogicException
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $input->getOption(self::OPTION_FIX) ? 'phpcbf' : 'phpcs';
        $cmd = 'vendor/bin/' . ($command) . ' -n -p --colors -s --standard=../tests/ruleset.xml';
        if ($input->getOption(self::OPTION_DIR)) {
            $cmd .= ' ' . $input->getOption(self::OPTION_DIR);
        }
        if ($input->getOption(self::OPTION_XML)) {
            $cmd .= ' --report=checkstyle --report-file=../tests/cs-data';
        }

        if ($input->getOption(self::OPTION_DRY_RUN)) {
            $output->writeln($cmd);

            return self::CODE_SUCCESS;
        }

        $process = new Process($cmd, $this->rootPath, null, null, 9600);
        $process->run(function ($type, $buffer) {
            echo $buffer;
        });

        return $process->getExitCode();
    }
}
