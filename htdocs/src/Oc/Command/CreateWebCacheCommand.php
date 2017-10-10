<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

namespace Oc\Command;

use Leafo\ScssPhp\Compiler;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateWebCacheCommand
 *
 * @package Oc\Command
 */
class CreateWebCacheCommand extends AbstractCommand
{
    const COMMAND_NAME = 'cache:web:create';

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
            ->setDescription('create legacy web caches');
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
        $output->writeln('generate style.css');

        $scss = new Compiler();
        $scss->setImportPaths(
            [
                __DIR__ . '/../../../vendor/twbs/bootstrap/scss/',
                __DIR__ . '/../../../theme/scss/',
            ]
        );

        file_put_contents(
            __DIR__ . '/../../../web/css/style.css',
            $scss->compile('@import "all.scss";')
        );

        $output->writeln('style.css generated');
    }
}
