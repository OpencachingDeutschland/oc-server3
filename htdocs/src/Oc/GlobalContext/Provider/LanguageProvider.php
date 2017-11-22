<?php

namespace Oc\GlobalContext\Provider;

use Oc\Language\LanguageService;
use Oc\Session\SessionDataInterface;
use Oc\User\UserProvider;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class LanguageProvider
 */
class LanguageProvider
{
    /**
     * @var LanguageService
     */
    private $languageService;

    /**
     * @var SessionDataInterface
     */
    private $sessionData;

    /**
     * @var string
     */
    private $defaultLanguage;

    /**
     * @var UserProvider
     */
    private $userProvider;

    /**
     * LanguageProvider constructor.
     *
     * @param LanguageService $languageService
     * @param SessionDataInterface $sessionData
     * @param UserProvider $userProvider
     * @param string $defaultLanguage
     */
    public function __construct(
        LanguageService $languageService,
        SessionDataInterface $sessionData,
        UserProvider $userProvider,
        $defaultLanguage
    ) {
        $this->languageService = $languageService;
        $this->sessionData = $sessionData;
        $this->defaultLanguage = $defaultLanguage;
        $this->userProvider = $userProvider;
    }

    /**
     * Determines the language of the user.
     *
     * @param Request $request
     *
     * @return string
     */
    public function getPreferredLanguage(Request $request)
    {
        // Determine session locale
        $sessionLocale = $this->sessionData->get('locale');

        if ($sessionLocale !== null) {
            return strtolower($sessionLocale);
        }

        $userLocale = $this->getUserLocale();

        if ($userLocale !== null) {
            return $userLocale;
        }

        $availableTranslations = $this->languageService->getAvailableTranslations();

        // Determine preferred language by Accepted-Language header and the available translations.
        $preferredLanguage = $request->getPreferredLanguage($availableTranslations);

        return $preferredLanguage ?: $this->defaultLanguage;
    }

    /**
     * Fetches the user locale.
     *
     * @return string|null
     */
    private function getUserLocale()
    {
        if ($user = $this->userProvider->bySession()) {
            return $user->language;
        }

        return null;
    }
}
