<?php
/***************************************************************************
 * For license information see LICENSE.md
 ***************************************************************************/

namespace Oc\Command;

use Oc\Translation\CrowdinImport;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;
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
            ->setName('translation:import-legacy-translation')
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
        $this->crowdinImport->importTranslations();
    }
}
