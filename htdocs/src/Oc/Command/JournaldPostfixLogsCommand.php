<?php
/***************************************************************************
 * For license information see LICENSE.md
 ***************************************************************************/

namespace Oc\Command;

use Doctrine\DBAL\Exception;
use Oc\Postfix\JournalLogs;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class JournaldPostfixLogsCommand extends SymfonyCommand
{
    /**
     * @var JournalLogs
     */
    private JournalLogs $journalLogs;

    public function __construct(JournalLogs $journalLogs)
    {
        parent::__construct();

        $this->journalLogs = $journalLogs;
    }

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('postfix:processing-logs')
            ->setDescription('process postfix logs for support');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): null
    {
        $this->journalLogs->processJournalLogs();

        return null;
    }
}
