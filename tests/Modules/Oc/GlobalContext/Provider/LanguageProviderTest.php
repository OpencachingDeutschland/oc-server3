<?php

namespace OcTest\Modules\Oc\GlobalContext\Provider;

use Oc\GlobalContext\Provider\LanguageProvider;
use Oc\Language\LanguageService;
use Oc\Session\SessionDataInterface;
use Oc\User\UserEntity;
use Oc\User\UserProvider;
use OcTest\Modules\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class LanguageProviderTest
 */
class LanguageProviderTest extends TestCase
{
    /**
     * Tests fetching the preferred language by session.
     */
    public function testGetPreferredLanguageBySession()
    {
        $provider = new LanguageProvider(
            $this->getLanguageServiceMock(false),
            $this->getSessionMockWithLocale('session'),
            $this->getUserProviderMockWithLocale(),
            'default'
        );

        $preferredLanguage = $provider->getPreferredLanguage(
            $this->createMock(Request::class)
        );

        self::assertSame('session', $preferredLanguage);
    }

    /**
     * Tests fetching the preferred language by user.
     */
    public function testGetPreferredLanguageByUser()
    {
        $provider = new LanguageProvider(
            $this->getLanguageServiceMock(false),
            $this->getSessionMockWithLocale(),
            $this->getUserProviderMockWithLocale('user'),
            'default'
        );

        $preferredLanguage = $provider->getPreferredLanguage(
            $this->createMock(Request::class)
        );

        self::assertSame('user', $preferredLanguage);
    }

    /**
     * Tests fetching the preferred language by user.
     */
    public function testGetPreferredLanguageByUserThatNotExists()
    {
        $provider = new LanguageProvider(
            $this->getLanguageServiceMock(),
            $this->getSessionMockWithLocale(),
            $this->getUserProviderMockWithLocale('user', false),
            'default'
        );

        $provider->getPreferredLanguage(
            $this->createMock(Request::class)
        );
    }

    /**
     * Tests fetching the preferred language by user.
     */
    public function testGetPreferredLanguageByAvailableTranslations()
    {
        $provider = new LanguageProvider(
            $this->getLanguageServiceMock(),
            $this->getSessionMockWithLocale(),
            $this->getUserProviderMockWithLocale(),
            'default'
        );

        $preferredLanguage = $provider->getPreferredLanguage(
            $this->getRequestMock()
        );

        self::assertSame('first', $preferredLanguage);
    }

    /**
     * Tests fetching the preferred language by user.
     */
    public function testGetPreferredLanguageWithFallback()
    {
        $provider = new LanguageProvider(
            $this->getLanguageServiceMock(),
            $this->getSessionMockWithLocale(),
            $this->getUserProviderMockWithLocale(),
            'default'
        );

        $preferredLanguage = $provider->getPreferredLanguage(
            $this->getRequestMock(true)
        );

        self::assertSame('default', $preferredLanguage);
    }

    /**
     * Prepares a request mock with getPreferredLanguage, returns the first element of getAvailableTranslations
     *
     * @param bool $localeFallback getPreferredLanguage returns null on true otherwise the first element
     *
     * @return Request
     */
    private function getRequestMock($localeFallback = false)
    {
        $availableTranslations = $this->getAvailableTranslations();

        $request = $this->createMock(Request::class);
        $request->method('getPreferredLanguage')
            ->with($availableTranslations)
            ->willReturn($localeFallback ? null : $availableTranslations[0]);

        return $request;
    }

    /**
     * Prepares LanguageService mock.
     *
     * @param bool $mustCall Must call getPreferredLanguage
     *
     * @return LanguageService
     */
    private function getLanguageServiceMock($mustCall = true)
    {
        $service = $this->createMock(LanguageService::class);

        $method = $service->expects($mustCall ? self::once() : self::never());

        $method->method('getAvailableTranslations')
            ->willReturn($this->getAvailableTranslations());

        return $service;
    }

    /**
     * Returns available translations to test with.
     *
     * @return string[]
     */
    private function getAvailableTranslations()
    {
        return ['first', 'second', 'third', 'fourth'];
    }

    /**
     * Prepares a UserProvider which returns a UserEntity with given locale.
     *
     * @param string|null $locale
     * @param bool $userExists Flag if the user exists
     *
     * @return UserProvider Provides the user by session
     */
    private function getUserProviderMockWithLocale($locale = null, $userExists = true)
    {
        $user = new UserEntity();
        $user->language = $locale;

        $provider = $this->createMock(UserProvider::class);
        $provider->method('bySession')
            ->willReturn($userExists ? $user : null);

        return $provider;
    }

    /**
     * Prepares the session data mock with given locale.
     *
     * @param string|null $locale
     *
     * @return SessionDataInterface
     */
    private function getSessionMockWithLocale($locale = null)
    {
        $session = $this->createMock(SessionDataInterface::class);
        $session->method('get')
                ->with('locale')
                ->willReturn($locale);

        return $session;
    }
}
