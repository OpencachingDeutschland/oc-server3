<?php

namespace Oc\Util\Error;

trait ErrorTrait
{
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @param string $text
     * @param null|string $context
     *
     * @return void
     */
    protected function addError($text, $context = null)
    {
        if ($context !== null) {
            $this->errors[$context] = $text;

            return;
        }
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
