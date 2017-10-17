<?php

namespace Oc\FieldNotes\Form;

use DateTime;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class UploadFormData
 *
 * @package Oc\FieldNotes\Form
 */
class UploadFormData
{
    /**
     * Field notes file
     *
     * @var UploadedFile
     */
    public $file;

    /**
     * Should ignore all field notes before $ignoreBeforeDate?
     *
     * @var bool
     */
    public $ignore;

    /**
     * Ignore field notes before this date.
     *
     * @var DateTime|null
     */
    public $ignoreBeforeDate;

    /**
     * Id of the user who uploaded the file.
     *
     * @var int
     */
    public $userId;
}
