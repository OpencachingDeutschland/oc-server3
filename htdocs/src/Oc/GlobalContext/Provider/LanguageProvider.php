<?php

namespace Oc\GlobalContext\Provider;

use Oc\Language\LanguageService;
use Oc\Session\SessionDataInterface;
use Oc\User\UserProvider;
use Symfony\Component\HttpFoundation\Request;

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

    public function __construct(
        LanguageService $languageService,
        SessionDataInterface $sessionData,
        UserProvider $userProvider,
        string $defaultLanguage
    ) {
        $this->languageService = $languageService;
        $this->sessionData = $sessionData;
        $this->defaultLanguage = $defaultLanguage;
        $this->userProvider = $userProvider;
    }

    /**
     * Returns default language configured in config.yml
     */
    public function getDefaultLanguage(): string
    {
        return $this->defaultLanguage;
    }

    /**
     * Determines the language of the user.
     */
    public function getPreferredLanguage(Request $request): string
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

        $locale = substr($preferredLanguage, 0, 2);

        return $locale ?: $this->defaultLanguage;
    }

    /**
     * Fetches the user locale.
     */
    private function getUserLocale(): ?string
    {
        if ($user = $this->userProvider->bySession()) {
            return $user->language;
        }

        return null;
    }
}
