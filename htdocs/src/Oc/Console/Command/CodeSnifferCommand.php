<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

namespace Oc\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class CodeSnifferCommand extends AbstractCommand
{
    const COMMAND_NAME      = 'code:sniff';

    const OPTION_DRY_RUN    = 'dry';
    const OPTION_FIX        = 'fix';

    protected function configure()
    {
        parent::configure();

        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Wrapper for phpcs');

        $this->addOption(self::OPTION_DRY_RUN, 'd', InputOption::VALUE_NONE, 'Dry run the command');
        $this->addOption(self::OPTION_FIX, 'f', InputOption::VALUE_NONE, 'Fix errors that can be fixed automatically');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $input->getOption(self::OPTION_FIX) ? 'phpcbf' : 'phpcs';
        $cmd = 'vendor/bin/'.($command).' --standard=PSR2 -p -n --colors src/';

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
