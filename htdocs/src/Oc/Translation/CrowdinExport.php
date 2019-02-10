<?php

namespace Oc\Translation;

use Doctrine\DBAL\Connection;

class CrowdinExport
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function exportTranslations(): void
    {
        $savePath = __DIR__ . '/../../../var/crowdin/';
        // Identifier;SourceString;Comment;langKey
        // key, source string, translation, context
        $select = '';
        $joins = '';
        foreach (['de', 'fr', 'nl', 'es', 'pl', 'it', 'ru'] as $languageKey) {
            $joins .= "\n" . ' LEFT JOIN sys_trans_text ' . $languageKey . ' ON ' . $languageKey . '.trans_id = source.trans_id AND ' . $languageKey . '.lang = "' . $languageKey . '" ';
            $select .= ', ' . $languageKey . '.text as ' . $languageKey . ' ';
        }

        $languageData = $this->connection->fetchAll(
            'SELECT source.trans_id AS identifer,
                    source.text AS source,
                    CONCAT(ref.line,\':\',ref.resource_name) AS comment
                    ' . $select . '
                 FROM sys_trans_text source
                 LEFT JOIN sys_trans_ref ref ON ref.trans_id = source.trans_id
                 ' . $joins . '
                 WHERE source.lang = "EN"
                 AND source.trans_id NOT IN (167, 171, 450, 453, 461, 465,466,470, 471, 472, 473, 604, 608, 681, 684, 699, 818, 830, 1298, 2265)
                 GROUP BY source.trans_id'
        );
        $csvFile = fopen($savePath . '/oc_legacy.csv', 'wb');
        fputcsv($csvFile, ['Identifier', 'SourceString', 'Comment', 'DE', 'FR', 'NL', 'ES', 'PL', 'IT', 'RU']);
        foreach ($languageData as $row) {
            fputcsv($csvFile, $row);
        }
        fclose($csvFile);
    }
}
