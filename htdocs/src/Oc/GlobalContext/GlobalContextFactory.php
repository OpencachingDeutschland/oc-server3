<?php

namespace Oc\GlobalContext;

use Oc\GlobalContext\Provider\LanguageProvider;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class GlobalContextFactory
 */
class GlobalContextFactory
{
    /**
     * @var LanguageProvider
     */
    private $languageProvider;

    /**
     * GlobalContextFactory constructor.
     *
     * @param LanguageProvider $languageProvider
     */
    public function __construct(LanguageProvider $languageProvider)
    {
        $this->languageProvider = $languageProvider;
    }

    /**
     * @param Request $request
     *
     * @return GlobalContext
     */
    public function createFromRequest(Request $request)
    {
        return new GlobalContext(
            $this->languageProvider->getPreferredLanguage($request)
        );
    }
}
