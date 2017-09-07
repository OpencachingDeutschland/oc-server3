<?php
/***************************************************************************
 * For license information see LICENSE.md
 ***************************************************************************/

namespace OcBundle\Command;

use okapi\Okapi;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OkapiCronjobsCommand extends ContainerAwareCommand
{
    const COMMAND_NAME = 'okapi:cronjobs';

    protected function configure()
    {
        parent::configure();

        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('executes okapi5 cronjobs');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        require_once __DIR__.'/../../../okapi/autoload.php';
        Okapi::execute_prerequest_cronjobs();
        Okapi::execute_cron5_cronjobs();
    }
}
