<?php

namespace AppBundle\Service\Interfaces;

use AppBundle\Entity\FieldNote;

interface FieldNoteServiceInterface extends ErrorInterface
{
    const FIELD_NOTE_DATETIME_FORMAT = 'Y-m-d\TH:i:s\Z';
    const LOG_TYPE = [
        'Found it' => FieldNote::LOG_TYPE_FOUND,
        "Didn't find it" => FieldNote::LOG_TYPE_NOT_FOUND,
        'Write note' => FieldNote::LOG_TYPE_NOTE,
        'Needs Maintenance' => FieldNote::LOG_TYPE_NEEDS_MAINTENANCE,
    ];

    /**
     * @param string $fileName
     * @param int $userId
     *
     * @return bool
     * @throws \AppBundle\Exception\WrongFileFormatException
     */
    public function importFromFile($fileName, $userId);

    /**
     * @param int $userId
     *
     * @return \DateTime|null
     */
    public function getLatestFieldNoteOrLogDate($userId);
}
