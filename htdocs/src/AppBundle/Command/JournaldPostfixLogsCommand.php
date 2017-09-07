<?php
/***************************************************************************
 * For license information see LICENSE.md
 ***************************************************************************/

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class JournaldPostfixLogsCommand extends ContainerAwareCommand
{
    const COMMAND_NAME = 'postfix:processing-logs';

    /**
     * @return void
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('process postfix logs for support');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $journal = $this->getContainer()->get('oc_bundle.postfix.journal_logs');
        $journal->processJournalLogs();
    }
}
