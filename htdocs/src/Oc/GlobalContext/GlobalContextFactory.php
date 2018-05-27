<?php

namespace Oc\GlobalContext;

use Oc\GlobalContext\Provider\LanguageProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

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
     * Creates a global context from given request.
     *
     * @param Request $request
     *
     * @return GlobalContext
     */
    public function createFromRequest(Request $request)
    {
        return new GlobalContext(
            $this->languageProvider->getDefaultLanguage(),
            $this->languageProvider->getPreferredLanguage($request)
        );
    }

    /**
     * Creates a global context from request stack.
     *
     * @param RequestStack $requestStack
     *
     * @return GlobalContext
     */
    public function createFromRequestStack(RequestStack $requestStack)
    {
        return $this->createFromRequest(
            $requestStack->getMasterRequest()
        );
    }
}
