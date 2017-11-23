<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

namespace Oc\Command;

use Exception;
use Leafo\ScssPhp\Compiler;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class CreateWebCacheCommand
 */
class CreateWebCacheCommand extends ContainerAwareCommand
{
    const COMMAND_NAME = 'cache:web:create';

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * Configures the command.
     *
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
        $this->output = $output;

        $projectDir = $this->getContainer()->getParameter('kernel.project_dir');

        $output->writeln('Generating WebCache');

        $paths = [
            $projectDir . '/web/assets',
            $projectDir . '/web/assets/css',
            $projectDir . '/web/assets/js',
            $projectDir . '/web/assets/images',
        ];

        foreach ($paths as $path) {
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
        }

        try {
            $this->compileCss($projectDir);
            $this->compileJs($projectDir);
            $this->copyImages($projectDir);
        } catch (Exception $e) {
            $this->output->writeln('<error>An exception occurred!</error>');
            $this->output->writeln('<error>' . $e->getMessage() . '</error>');
        }

        $output->writeln('WebCache generated');
    }

    /**
     * Compiles js to one file.
     *
     * @param string $projectDir
     */
    private function compileJs($projectDir)
    {
        $this->output->writeln('Generating javascript');

        $applicationJsPath = $projectDir . '/app/Resources/assets/js/';

        if (!file_exists($applicationJsPath)) {
            $this->output->writeln('<comment>- Javascript directory not found!</comment>');

            return;
        }

        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($applicationJsPath));

        $js = '';

        /**
         * @var SplFileInfo
         */
        foreach ($rii as $file) {
            if ($file->isDir() || $file->getExtension() !== 'js') {
                continue;
            }

            $js .= file_get_contents($file->getRealPath()) . PHP_EOL;
        }

        file_put_contents($projectDir . '/web/assets/js/main.js', $js);

        $this->output->writeln('<info>- Javascript generated</info>');
    }

    /**
     * Compiles scss to one file.
     *
     * @param string $projectDir
     */
    private function compileCss($projectDir)
    {
        $this->output->writeln('Generating stylesheets');

        $applicationScssPath = $projectDir . '/app/Resources/assets/scss/';

        $scss = new Compiler();
        $scss->setIgnoreErrors(true);
        $scss->addImportPath($applicationScssPath);
        $scss->addImportPath(function ($path) use ($projectDir) {
            //Check for tilde as this refers to the node_modules dir
            if (strpos($path, '~') === 0) {
                $path = str_replace(
                    ['~', 'bootstrap'],
                    [$projectDir . '/vendor/', 'twbs/bootstrap'],
                    $path
                );

                $path .= '.scss';

                //if file does not exist, try with underscore before filename
                if (!file_exists($path)) {
                    $chunks = explode('/', $path);

                    end($chunks);
                    $lastKey = key($chunks);
                    reset($chunks);

                    $chunks[$lastKey] = '_' . $chunks[$lastKey];

                    $path = implode('/', $chunks);
                }

                if (!file_exists($path)) {
                    return null;
                }
            }

            return $path;
        });

        file_put_contents(
            $projectDir . '/web/assets/css/style.min.css',
            $scss->compile(file_get_contents($applicationScssPath . '/all.scss'))
        );

        file_put_contents(
            $projectDir . '/web/assets/css/legacy.min.css',
            $scss->compile(file_get_contents($applicationScssPath . '/legacy.scss'))
        );

        $this->output->writeln('<info>- Stylesheets generated</info>');
    }

    /**
     * @param string $projectDir
     */
    private function copyImages($projectDir)
    {
        $this->output->writeln('Copying images');

        $source = $projectDir . '/app/Resources/assets/images';
        $destination = $projectDir . '/web/assets/images';

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                mkdir($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                copy($item, $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }

        $this->output->writeln('<info>- Images copied</info>');
    }
}
