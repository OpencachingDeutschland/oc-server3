<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

namespace Oc\Command;

use InvalidArgumentException;
use SimpleXMLElement;
use Symfony\Component\Console\Exception\InvalidArgumentException as ConsoleInvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CodeCoverageCommand
 */
class CodeCoverageCommand extends AbstractCommand
{
    const COMMAND_NAME = 'build:code-coverage';

    /**
     * Configures the command.
     *
     *
     * @throws ConsoleInvalidArgumentException
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('display the code coverage from the last phpunit run');
    }

    /**
     * Executes the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws InvalidArgumentException
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputFile = __DIR__ . '/../../../../build/logs/clover.xml';

        if (!file_exists($inputFile)) {
            throw new InvalidArgumentException('Invalid input file provided');
        }

        $xml = new SimpleXMLElement(file_get_contents($inputFile));
        $metrics = $xml->xpath('//metrics');
        $totalElements = 0;
        $checkedElements = 0;

        foreach ($metrics as $metric) {
            $totalElements += (int) $metric['elements'];
            $checkedElements += (int) $metric['coveredelements'];
        }

        $coverage = ($checkedElements / $totalElements) * 100;

        $output->writeln('Code coverage is ' . $coverage . '%');

        return self::CODE_SUCCESS;
    }
}
