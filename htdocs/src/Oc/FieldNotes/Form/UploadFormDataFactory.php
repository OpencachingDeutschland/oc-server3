<?php

namespace Oc\FieldNotes\Form;

use DateTime;
use Oc\FieldNotes\Persistence\FieldNoteService;
use Oc\GeoCache\Persistence\GeoCacheLog\GeoCacheLogService;

class UploadFormDataFactory
{
    /**
     * @var FieldNoteService
     */
    private $fieldNoteService;

    /**
     * @var GeoCacheLogService
     */
    private $geoCacheLogService;

    public function __construct(FieldNoteService $fieldNoteService, GeoCacheLogService $geoCacheLogService)
    {
        $this->fieldNoteService = $fieldNoteService;
        $this->geoCacheLogService = $geoCacheLogService;
    }

    /**
     * Creates a UploadFormData by given user id.
     */
    public function create(int $userId): UploadFormData
    {
        $uploadFormData = new UploadFormData();

        $uploadFormData->userId = $userId;
        $uploadFormData->ignoreBeforeDate = $this->getLatestLogOrFieldNoteDate($userId);

        return $uploadFormData;
    }

    /**
     * Fetches the latest log or field note date.
     */
    private function getLatestLogOrFieldNoteDate(int $userId): ?DateTime
    {
        $fieldNoteDate = $this->getLatestFieldNoteDate($userId);

        $geoCacheLogDate = $this->getLatestLogDate($userId);

        return max($fieldNoteDate, $geoCacheLogDate);
    }

    /**
     * Returns the latest log date.
     */
    private function getLatestLogDate(int $userId): ?DateTime
    {
        $geoCacheLogDate = null;
        $geoCacheLog = $this->geoCacheLogService->getLatestUserLog($userId);

        if ($geoCacheLog) {
            $geoCacheLogDate = $geoCacheLog->date;
        }

        return $geoCacheLogDate;
    }

    /**
     * Fetches the latest field note date.
     */
    private function getLatestFieldNoteDate(int $userId): ?DateTime
    {
        $fieldNoteDate = null;
        $fieldNote = $this->fieldNoteService->getLatestUserFieldNote($userId);

        if ($fieldNote) {
            $fieldNoteDate = $fieldNote->date;
        }

        return $fieldNoteDate;
    }
}
