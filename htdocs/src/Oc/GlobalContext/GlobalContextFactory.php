<?php

namespace Oc\GlobalContext;

use Oc\GlobalContext\Provider\LanguageProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class GlobalContextFactory
{
    /**
     * @var LanguageProvider
     */
    private $languageProvider;

    public function __construct(LanguageProvider $languageProvider)
    {
        $this->languageProvider = $languageProvider;
    }

    /**
     * Creates a global context from given request.
     */
    public function createFromRequest(Request $request): GlobalContext
    {
        return new GlobalContext(
            $this->languageProvider->getDefaultLanguage(),
            $this->languageProvider->getPreferredLanguage($request)
        );
    }

    /**
     * Creates a global context from request stack.t
     */
    public function createFromRequestStack(RequestStack $requestStack): GlobalContext
    {
        return $this->createFromRequest(
            $requestStack->getMasterRequest()
        );
    }
}
