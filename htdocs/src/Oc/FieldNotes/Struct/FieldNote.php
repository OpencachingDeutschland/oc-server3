<?php

namespace Oc\FieldNotes\Struct;

use Oc\FieldNotes\Validator\Constraints as FieldNoteAssert;
use Oc\Validator\Constraints as OcAssert;
use Symfony\Component\Validator\Constraints as Assert;

class FieldNote
{
    /**
     * @var string
     *
     * @Assert\NotBlank(message="oc.field_notes.struct.field_note.waypoint.not_blank")
     * @OcAssert\PersistedWaypoint
     */
    public $waypoint;

    /**
     * @var string
     *
     * @FieldNoteAssert\DateTime
     */
    public $noticedAt;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="oc.field_notes.struct.field_note.log_type.not_blank")
     * @FieldNoteAssert\LogType
     */
    public $logType;

    /**
     * @var string
     */
    public $notice;
}
