<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

namespace Oc\Command;

use OcLegacy\Cache\WebCache;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ClearWebCacheCommand
 *
 * @package Oc\Command
 */
class ClearWebCacheCommand extends AbstractCommand
{
    const COMMAND_NAME = 'cache:clear-web-cache';

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
            ->setDescription('clear legacy web caches');
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
        $output->writeln('Delete cached files');

        $webCache = new WebCache();
        $webCache->clearCache();

        return self::CODE_SUCCESS;
    }
}
