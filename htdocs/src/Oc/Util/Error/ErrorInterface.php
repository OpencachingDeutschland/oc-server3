<?php

namespace Oc\Util\Error;

interface ErrorInterface
{
    /**
     * @return bool
     */
    public function hasErrors();

    /**
     * @return array
     */
    public function getErrors();
}
