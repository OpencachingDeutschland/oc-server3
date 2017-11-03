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

    /**
     * @param FieldNoteService $fieldNoteService
     * @param GeoCacheLogService $geoCacheLogService
     */
    public function __construct(FieldNoteService $fieldNoteService, GeoCacheLogService $geoCacheLogService)
    {
        $this->fieldNoteService = $fieldNoteService;
        $this->geoCacheLogService = $geoCacheLogService;
    }

    /**
     * Creates a UploadFormData by given user id.
     *
     * @param int $userId
     *
     * @return UploadFormData
     */
    public function create($userId)
    {
        $uploadFormData = new UploadFormData();

        $uploadFormData->userId = $userId;
        $uploadFormData->ignoreBeforeDate = $this->getLatestLogOrFieldNoteDate($userId);

        return $uploadFormData;
    }

    /**
     * Fetches the latest log or field note date.
     *
     * @param int $userId
     *
     * @return string
     */
    private function getLatestLogOrFieldNoteDate($userId)
    {
        $fieldNoteDate = $this->getLatestFieldNoteDate($userId);

        $geoCacheLogDate = $this->getLatestLogDate($userId);

        return max($fieldNoteDate, $geoCacheLogDate);
    }

    /**
     * @param int $userId
     *
     * @return DateTime|null
     */
    private function getLatestLogDate($userId)
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
     *
     * @param int $userId
     *
     * @return DateTime|null
     */
    private function getLatestFieldNoteDate($userId)
    {
        $fieldNoteDate = null;
        $fieldNote = $this->fieldNoteService->getLatestUserFieldNote($userId);

        if ($fieldNote) {
            $fieldNoteDate = $fieldNote->date;
        }

        return $fieldNoteDate;
    }
}
