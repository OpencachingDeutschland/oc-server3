<?php
/***************************************************************************
 * For license information see LICENSE.md
 ***************************************************************************/

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportLegacyTranslationCommand extends ContainerAwareCommand
{
    const COMMAND_NAME = 'translation:export-legacy-translation';

    protected function configure()
    {
        parent::configure();

        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('export translation legacy translation system to crowdin');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('oc.translation.crowdin_export')->exportTranslations();
    }
}
