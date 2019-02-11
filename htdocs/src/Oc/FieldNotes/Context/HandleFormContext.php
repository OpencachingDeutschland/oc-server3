<?php

namespace Oc\FieldNotes\Context;

class HandleFormContext
{
    /**
     * @var bool
     */
    private $success;

    /**
     * @var string[]
     */
    private $errors;

    public function __construct($success, array $errors)
    {
        $this->success = $success;
        $this->errors = $errors;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * List of translated error message to display in the frontend.
     *
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
