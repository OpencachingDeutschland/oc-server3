<?php

namespace AppBundle\Form\DataProvider;

use AppBundle\Form\UploadFieldNotesType;
use AppBundle\Service\Interfaces\FieldNoteServiceInterface;
use AppBundle\Util\DateUtil;

class UploadFieldNotesDataProvider
{
    /**
     * @var \AppBundle\Service\Interfaces\FieldNoteServiceInterface
     */
    protected $fieldNoteService;

    /**
     * @var int
     */
    protected $userId;

    /**
     * UploadFieldNotesDataProvider constructor.
     *
     * @param \AppBundle\Service\Interfaces\FieldNoteServiceInterface $fieldNoteService
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
            UploadFieldNotesType::FIELD_IGNORE_DATE => $date,
        ];
    }
}
