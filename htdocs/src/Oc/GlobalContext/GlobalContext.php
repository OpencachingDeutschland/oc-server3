<?php

namespace Oc\GlobalContext;

/**
 * Class GlobalContext
 */
class GlobalContext
{
    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * GlobalContext constructor.
     *
     * @param string $defaultLocale
     * @param string $locale
     */
    public function __construct($defaultLocale, $locale)
    {
        $this->defaultLocale = $defaultLocale;
        $this->locale = $locale;
    }

    /**
     * Returns the default locale of the application.
     *
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
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
