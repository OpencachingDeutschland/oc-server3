<?php
/***************************************************************************
 * For license information see LICENSE.md
 ***************************************************************************/

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * very quick and dirty solution to import crowdin snippets into the legacy translation system
 */
class ImportLegacyTranslationCommand extends ContainerAwareCommand
{
    const COMMAND_NAME = 'translation:update-legacy-translation';

    protected function configure()
    {
        parent::configure();

        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('import translation from crowdin into legacy translation system');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('oc.translation.crowdin_import')->importTranslations();
    }
}
