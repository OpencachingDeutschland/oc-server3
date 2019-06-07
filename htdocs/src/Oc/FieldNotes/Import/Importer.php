<?php

namespace Oc\FieldNotes\Import;

use DateTime;
use DateTimeZone;
use Oc\FieldNotes\Enum\LogType;
use Oc\FieldNotes\Import\Context\ImportContext;
use Oc\FieldNotes\Persistence\FieldNoteEntity;
use Oc\FieldNotes\Persistence\FieldNoteService;
use Oc\FieldNotes\Struct\FieldNote;
use Oc\GeoCache\Persistence\GeoCache\GeoCacheService;

class Importer
{
    /**
     * @var GeoCacheService
     */
    private $geoCacheService;

    /**
     * @var FieldNoteService
     */
    private $fieldNoteService;

    public function __construct(
        GeoCacheService $geoCacheService,
        FieldNoteService $fieldNoteService
    ) {
        $this->geoCacheService = $geoCacheService;
        $this->fieldNoteService = $fieldNoteService;
    }

    /**
     * Import by given context.
     */
    public function import(ImportContext $context): void
    {
        $uploadFormData = $context->getFormData();

        /**
         * @var FieldNote
         */
        foreach ($context->getFieldNotes() as $fieldNote) {
            $date = new DateTime($fieldNote->noticedAt, new DateTimeZone('UTC'));
            $date->setTimezone(new DateTimeZone(date_default_timezone_get()));

            if ($uploadFormData->ignore && $date <= $uploadFormData->ignoreBeforeDate) {
                continue;
            }

            $geoCache = $this->geoCacheService->fetchByWaypoint(
                $fieldNote->waypoint
            );

            $entity = new FieldNoteEntity();
            $entity->userId = $uploadFormData->userId;
            $entity->geocacheId = $geoCache->cacheId;
            $entity->type = LogType::guess($fieldNote->logType);
            $entity->date = $date;
            $entity->text = $fieldNote->notice;

            $this->fieldNoteService->create($entity);
        }
    }
}
