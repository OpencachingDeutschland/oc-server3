<?php

namespace Oc\FieldNotes\Import;

use League\Csv\Reader;
use Oc\FieldNotes\Exception\FileFormatException;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Dear brave soul
 * Once you are done trying to "optimize" this class,
 * and break everything but didn't recognize in first place,
 * please increment the following counter as a warning
 * for the next brave soul
 * total_hours_wasted = 42
 */
class FileParser
{
    /**
     * @var StructMapper
     */
    private $structMapper;

    public function __construct(StructMapper $structMapper)
    {
        $this->structMapper = $structMapper;
    }

    /**
     * @throws FileFormatException
     */
    public function parseFile(File $file): array
    {
        $csv = Reader::createFromPath($file->getRealPath());
        $csv->setDelimiter(',');
        $csv->setEnclosure('"');

        $rows = $this->getRowsFromCsv($csv);

        return $this->structMapper->map($rows);
    }

    private function getRowsFromCsv(Reader $csv): array
    {
        $lines = [];
        $rows = [];
        $content = $csv->getContent();
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-16LE');

        $rawLines = array_filter(explode("\n", $content));
        $tmpString = '';
        // is used to support line breaks in the comment section
        foreach ($rawLines as $key => $rawLine) {
            $columnCheck = count(str_getcsv($rawLines[$key + 1]));
            $tmpString .= $rawLine;
            if ($columnCheck === 4 || $rawLines[$key + 1] === null) {
                $lines[] = $tmpString;
                $tmpString = '';
            }
        }

        foreach ($lines as $line) {
            $row = str_getcsv($line, ',', '"');
            $row = array_map('trim', $row);

            if (count($row) < 4) {
                throw new FileFormatException('A row contains more or less than 4 columns');
            }

            $rows[] = $row;
        }

        return $rows;
    }
}
