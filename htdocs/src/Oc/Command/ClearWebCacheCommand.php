<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

namespace Oc\Command;

use OcLegacy\Cache\WebCache;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearWebCacheCommand extends AbstractCommand
{
    /**
     * @var string
     */
    const COMMAND_NAME = 'cache:clear-web-cache';

    /**
     * Configures the command.
     *
     * @throws InvalidArgumentException
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('clear legacy web caches');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $output->writeln('Delete cached files');

        $webCache = new WebCache();
        $webCache->clearCache();

        return self::CODE_SUCCESS;
    }
}
