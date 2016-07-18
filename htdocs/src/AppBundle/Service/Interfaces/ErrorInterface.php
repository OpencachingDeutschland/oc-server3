<?php

namespace AppBundle\Service\Interfaces;

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
