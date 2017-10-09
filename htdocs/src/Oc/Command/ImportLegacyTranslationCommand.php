<?php
/***************************************************************************
 * For license information see LICENSE.md
 ***************************************************************************/

namespace Oc\Command;

use Oc\Translation\CrowdinImport;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportLegacyTranslationCommand.
 *
 * Very quick and dirty solution to import crowdin snippets into the legacy translation system
 *
 * @package Oc\Command
 */
class ImportLegacyTranslationCommand extends ContainerAwareCommand
{
    const COMMAND_NAME = 'translation:import-legacy-translation';

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
            ->setDescription('import translation from crowdin into legacy translation system');
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
        $this->getContainer()->get(CrowdinImport::class)->importTranslations();
    }
}
