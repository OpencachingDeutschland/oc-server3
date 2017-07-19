<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

namespace AppBundle\Command;

use Leafo\ScssPhp\Compiler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateWebCacheCommand extends AbstractCommand
{
    const COMMAND_NAME = 'cache:web:create';

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
     * @return void
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
