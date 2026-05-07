<?php
/***************************************************************************
 * For license information see LICENSE.md
 ***************************************************************************/

namespace Oc\Command;

use okapi\core\Okapi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class OkapiCronjobsCommand
 */
class OkapiCronjobsCommand extends Command
{
    /**
     * @var string
     */
    public const COMMAND_NAME = 'okapi:cronjobs';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('executes okapi5 cronjobs');
    }

    protected function execute(InputInterface $input, OutputInterface $output): null
    {
        require_once __DIR__ . '/../../../okapi/autoload.php';
        Okapi::execute_prerequest_cronjobs();
        Okapi::execute_cron5_cronjobs();

        return null;
    }
}
