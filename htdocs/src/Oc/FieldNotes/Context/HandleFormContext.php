<?php

namespace Oc\FieldNotes\Context;

/**
 * Class HandleFormContext
 *
 * @package Oc\FieldNotes\Context
 */
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

    /**
     * HandleFormContext constructor.
     *
     * @param bool $success
     * @param array $errors
     */
    public function __construct($success, array $errors)
    {
        $this->success = $success;
        $this->errors = $errors;
    }

    /**
     * Was the handling of the form successful?
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * List of translated error message to display in the frontend.
     *
     * @return string[]
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
