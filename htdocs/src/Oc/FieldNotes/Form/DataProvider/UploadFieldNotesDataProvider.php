<?php

namespace Oc\FieldNotes\Form\DataProvider;

use Oc\FieldNotes\FieldNoteServiceInterface;
use Oc\FieldNotes\Form\UploadFieldNotesType;
use Oc\Util\DateUtil;

class UploadFieldNotesDataProvider
{
    /**
     * @var FieldNoteServiceInterface
     */
    protected $fieldNoteService;

    /**
     * @var int
     */
    protected $userId;

    /**
     * UploadFieldNotesDataProvider constructor.
     *
     * @param FieldNoteServiceInterface $fieldNoteService
     */
    public function __construct(FieldNoteServiceInterface $fieldNoteService)
    {
        $this->fieldNoteService = $fieldNoteService;
    }

    /**
     * @param int $userId
     *
     * @return array
     */
    public function getData($userId)
    {
        $date = $this->fieldNoteService->getLatestFieldNoteOrLogDate($userId);
        if ($date !== null) {
            $date = $date->format(DateUtil::DATE_FORMAT_MYSQL);
        }

        return [
            UploadFieldNotesType::FIELD_IGNORE_DATE => $date
        ];
    }
}
