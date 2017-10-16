<?php
/***************************************************************************
 * For license information see LICENSE.md
 ***************************************************************************/

namespace Oc\Command;

use Oc\Postfix\JournalLogs;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class JournaldPostfixLogsCommand
 *
 * @package Oc\Command
 */
class JournaldPostfixLogsCommand extends SymfonyCommand
{
    /**
     * @var JournalLogs
     */
    private $journalLogs;

    public function __construct(JournalLogs $journalLogs)
    {
        parent::__construct();
        $this->journalLogs = $journalLogs;
    }

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
            ->setName('postfix:processing-logs')
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
        $this->journalLogs->processJournalLogs();
    }
}
