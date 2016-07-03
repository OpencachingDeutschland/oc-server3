<?php

namespace AppBundle\Service\Traits;

trait ErrorTrait
{
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @param string $text
     *
     * @return void
     */
    protected function addError($text)
    {
        $this->errors[] = $text;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
