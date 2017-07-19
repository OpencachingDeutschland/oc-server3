<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

namespace AppBundle\Command;

use Oc\Cache\WebCache;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearWebCacheCommand extends AbstractCommand
{
    const COMMAND_NAME = 'cache:clear-web-cache';

    /**
     * @return void
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('clear legacy web caches');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
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
