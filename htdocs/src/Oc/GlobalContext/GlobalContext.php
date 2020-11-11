<?php

namespace Oc\GlobalContext;

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

    public function __construct(string $defaultLocale, string $locale)
    {
        $this->defaultLocale = $defaultLocale;
        $this->locale = $locale;
    }

    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
