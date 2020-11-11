<?php

namespace Oc\FieldNotes\Import\Context;

use Oc\FieldNotes\Form\UploadFormData;
use Oc\FieldNotes\Struct\FieldNote;

class ImportContext
{
    /**
     * @var FieldNote[]
     */
    private $fieldNotes;

    /**
     * @var UploadFormData
     */
    private $formData;

    /**
     * @param FieldNote[] $fieldNotes
     */
    public function __construct(array $fieldNotes, UploadFormData $formData)
    {
        $this->fieldNotes = $fieldNotes;
        $this->formData = $formData;
    }

    /**
     * Returns field notes to be imported.
     *
     * @return FieldNote[]
     */
    public function getFieldNotes(): array
    {
        return $this->fieldNotes;
    }

    public function getFormData(): UploadFormData
    {
        return $this->formData;
    }
}
