<?php
/***************************************************************************
 * For license information see LICENSE.md
 ***************************************************************************/

namespace Oc\Command;

use Oc\Translation\CrowdinImport;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Very quick and dirty solution to import crowdin snippets into the legacy translation system
 */
class ImportLegacyTranslationCommand extends SymfonyCommand
{
    /**
     * @var CrowdinImport
     */
    private $crowdinImport;

    public function __construct(CrowdinImport $crowdinImport)
    {
        parent::__construct();

        $this->crowdinImport = $crowdinImport;
    }

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('translation:import-legacy-translation')
            ->setDescription('import translation from crowdin into legacy translation system');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->crowdinImport->importTranslations();

        return null;
    }
}
