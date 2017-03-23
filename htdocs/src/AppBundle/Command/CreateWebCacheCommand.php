<?php
/***************************************************************************
 * for license information see doc/license.txt
 ***************************************************************************/

namespace AppBundle\Command;

use Oc\Cache\WebCache;
use Oc\Session\SessionDataCookie;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateWebCacheCommand extends AbstractCommand
{
    const COMMAND_NAME = 'cache:create-web-cache';

    /**
     * @return void
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('create legacy web caches');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('not complete implemented yet');
        return self::CODE_ERROR;
        // TODO implement completely
        global $opt, $cookie;

        if (!isset($opt['rootpath'])) {
            $opt['rootpath'] = __DIR__ . '/../../../../htdocs/';
        }

        $cookie = new SessionDataCookie();

        require_once __DIR__ . '/../../../lib2/cli.inc.php';

        $webCache = new WebCache();

        $output->writeln('Create menu cache file');
        $webCache->createMenuCache();

        $output->writeln('Create label cache file');
        $webCache->createLabelCache();

        $output->writeln('Precompiling template files');
        $webCache->preCompileAllTemplates();
    }
}
