<?php
/***************************************************************************
 * For license information see LICENSE.md
 ***************************************************************************/

namespace Oc\Command;

use Doctrine\DBAL\Exception;
use Oc\Translation\CrowdinExport;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportLegacyTranslationCommand extends SymfonyCommand
{
    /**
     * @var CrowdinExport
     */
    private $crowdinExport;

    public function __construct(CrowdinExport $crowdinExport)
    {
        parent::__construct();
        $this->crowdinExport = $crowdinExport;
    }

    /**
     * Configures the command.
     *
     * @throws InvalidArgumentException
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('translation:export-legacy-translation')
            ->setDescription('export translation legacy translation system to crowdin');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): null
    {
        $this->crowdinExport->exportTranslations();

        return null;
    }
}
