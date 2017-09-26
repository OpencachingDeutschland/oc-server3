<?php

namespace Oc\Translation;

use Doctrine\DBAL\Connection;

/**
 * very quick and dirty solution to import crowdin snippets into the legacy translation system
 */
class CrowdinImport
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function importTranslations()
    {
        $translationArray = $this
            ->readCrowdinCsv(__DIR__ . '/../../../app/Resources/translations/legacycode/oc_legacy.de.csv');

        foreach ($translationArray as $translation) {
            foreach (['de', 'fr', 'nl', 'es', 'pl', 'it', 'ru'] as $languageKey) {
                $this->connection->executeUpdate(
                    'UPDATE sys_trans_text SET `text` = :text
                     WHERE lang = :langKey AND trans_id = :identifier',
                    [
                        'text' => $translation->$languageKey,
                        'id' => $translation->identifier,
                        'langKey' => $languageKey,
                        'identifier' => $translation->identifier
                    ]
                );
            }
        }
    }

    /**
     * @param $path
     * @return TranslationStruct[]
     */
    private function readCrowdinCsv($path)
    {
        $csvHeadline = [];
        $translationStructs = [];
        if (($handle = fopen($path, 'rb')) !== false) {
            while (($data = fgetcsv($handle, 0, ';')) !== false) {
                if (!$csvHeadline) {
                    $csvHeadline = array_values($data);
                    continue;
                }

                $translationStructs[] = (new TranslationStruct())->fromCsvArray(array_combine($csvHeadline, $data));
            }
            fclose($handle);
        }

        return $translationStructs;
    }
}
