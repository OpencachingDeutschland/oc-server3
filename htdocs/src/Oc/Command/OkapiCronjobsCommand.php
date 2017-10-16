<?php
/***************************************************************************
 * For license information see LICENSE.md
 ***************************************************************************/

namespace Oc\Command;

use okapi\core\Okapi;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class OkapiCronjobsCommand
 *
 * @package Oc\Command
 */
class OkapiCronjobsCommand extends ContainerAwareCommand
{
    const COMMAND_NAME = 'okapi:cronjobs';

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
            ->setDescription('executes okapi5 cronjobs');
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
        require_once __DIR__.'/../../../okapi/autoload.php';
        Okapi::execute_prerequest_cronjobs();
        Okapi::execute_cron5_cronjobs();
    }
}
