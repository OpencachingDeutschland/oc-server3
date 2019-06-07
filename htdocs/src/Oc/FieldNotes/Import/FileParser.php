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
     * @param File $file
     * @return array
     * @throws FileFormatException
     */
    public function parseFile(File $file): array
    {
        $content = $this->getSanitizedFileContent($file);

        $rows = $this->getRowsFromCsv($content);

        return $this->structMapper->map($rows);
    }

    private function getSanitizedFileContent(File $file): string
    {
        $content = file_get_contents($file->getRealPath());
        $content = str_replace("\xFF\xFE", '', $content);
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-16LE');
        $content = str_replace("\r\n", "\n", $content);

        return $content;
    }

    private function getRowsFromCsv(string $content): array
    {
        $lines = array_filter(explode("\n", $content));

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
