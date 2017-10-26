<?php

namespace Oc\FieldNotes\Import\Context;

use Oc\FieldNotes\Form\UploadFormData;
use Oc\FieldNotes\Struct\FieldNote;

class ImportContext
{
    /**
     * @var array
     */
    private $fieldNotes;

    /**
     * @var UploadFormData
     */
    private $formData;

    /**
     * @param FieldNote[] $fieldNotes
     * @param UploadFormData $formData
     */
    public function __construct(array $fieldNotes, UploadFormData $formData)
    {
        $this->fieldNotes = $fieldNotes;
        $this->formData = $formData;
    }

    /**
     * Returns field notes to be imported.
     *
     * @return array
     */
    public function getFieldNotes()
    {
        return $this->fieldNotes;
    }

    /**
     * Returns the UploadFormData.
     *
     * @return UploadFormData
     */
    public function getFormData()
    {
        return $this->formData;
    }
}
