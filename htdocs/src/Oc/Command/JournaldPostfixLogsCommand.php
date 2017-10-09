<?php
/***************************************************************************
 * For license information see LICENSE.md
 ***************************************************************************/

namespace Oc\Command;

use Oc\Postfix\JournalLogs;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class JournaldPostfixLogsCommand
 *
 * @package Oc\Command
 */
class JournaldPostfixLogsCommand extends ContainerAwareCommand
{
    const COMMAND_NAME = 'postfix:processing-logs';

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
            ->setDescription('process postfix logs for support');
    }

    /**
     * Executes the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $journal = $this->getContainer()->get(JournalLogs::class);
        $journal->processJournalLogs();
    }
}
