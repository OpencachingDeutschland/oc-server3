<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 ***************************************************************************/

namespace AppBundle\Command;

use InvalidArgumentException;
use SimpleXMLElement;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CodeCoverageCommand extends AbstractCommand
{
    const COMMAND_NAME = 'build:code-coverage';

    /**
     * @return void
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('display the code coverage from the last phpunit run');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null
     * @throws \InvalidArgumentException
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
            $totalElements += (int)$metric['elements'];
            $checkedElements += (int)$metric['coveredelements'];
        }

        $coverage = ($checkedElements / $totalElements) * 100;

        $output->writeln('Code coverage is ' . $coverage . '%');

        return self::CODE_SUCCESS;
    }
}
