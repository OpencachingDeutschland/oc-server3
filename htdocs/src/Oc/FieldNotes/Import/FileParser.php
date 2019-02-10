<?php

namespace Oc\FieldNotes\Import;

use Oc\FieldNotes\Exception\FileFormatException;
use Symfony\Component\HttpFoundation\File\File;

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
     * Parses the given file.
     */
    public function parseFile(File $file): array
    {
        $content = $this->getSanitizedFileContent($file);

        $rows = $this->getRowsFromCsv($content);

        return $this->structMapper->map($rows);
    }

    /**
     * Fetches the content of the file and returns it.
     */
    private function getSanitizedFileContent(File $file): string
    {
        $content = file_get_contents($file->getRealPath());
        $content = str_replace("\xFF\xFE", '', $content);
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-16LE');
        $content = str_replace("\r\n", "\n", $content);

        return $content;
    }

    /**
     * Fetches rows from csv content.
     */
    private function getRowsFromCsv(string $content): array
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
