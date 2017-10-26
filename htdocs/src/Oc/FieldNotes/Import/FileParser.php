<?php

namespace Oc\FieldNotes\Import;

use Oc\FieldNotes\Exception\FileFormatException;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class FileParser
 *
 * @package Oc\FieldNotes\Import
 */
class FileParser
{
    /**
     * @var StructMapper
     */
    private $structMapper;

    /**
     * FileParser constructor.
     *
     * @param StructMapper $structMapper
     */
    public function __construct(StructMapper $structMapper)
    {
        $this->structMapper = $structMapper;
    }

    /**
     * Parses the given file
     *
     * @param File $file
     *
     * @return array
     *
     * @throws FileFormatException
     */
    public function parseFile(File $file)
    {
        $content = $this->getSanitizedFileContent($file);

        $rows = $this->getRowsFromCsv($content);

        return $this->structMapper->map($rows);
    }

    /**
     * Fetches the content of the file and returns it.
     *
     * @param File $file
     *
     * @return string
     */
    private function getSanitizedFileContent(File $file)
    {
        $content = file_get_contents($file->getRealPath());
        $content = str_replace("\xFF\xFE", '', $content);
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-16LE');
        $content = str_replace("\r\n", "\n", $content);

        return $content;
    }

    /**
     * Fetches rows from csv content.
     *
     * @param string $content
     *
     * @return array
     *
     * @throws FileFormatException
     */
    private function getRowsFromCsv($content)
    {
        $lines = explode("\n", $content);
        $lines = array_filter($lines);

        $rows = [];

        foreach ($lines as $line) {
            $row = str_getcsv($line);
            $row = array_map('trim', $row);

            if (count($row) !== 4) {
                throw new FileFormatException('A row contains more or less than 4 columns');
            }

            $rows[] = $row;
        }

        return $rows;
    }
}
