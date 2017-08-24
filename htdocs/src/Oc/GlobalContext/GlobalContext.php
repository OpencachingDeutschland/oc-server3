<?php

namespace Oc\GlobalContext;

/**
 * Class GlobalContext
 *
 * @package Oc\GlobalContext
 */
class GlobalContext
{
    /**
     * @var string
     */
    private $locale;

    /**
     * GlobalContext constructor.
     *
     * @param string $locale
     */
    public function __construct($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Returns the global locale of the application.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
}
