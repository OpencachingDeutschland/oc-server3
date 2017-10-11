<?php

namespace OcLegacy\Security;

use OcLegacy\User\LegacyUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class LegacyAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * Called on every request. Return whatever credentials you want,
     * or null to stop authentication.
     *
     * @param Request $request
     *
     * @return mixed|null
     */
    public function getCredentials(Request $request)
    {
        // What you return here will be passed to getUser() as $credentials
        $login = $GLOBALS['login'];

        if (!$login->logged_in()) {
            return false;
        }

        return [
            'id' => $login->userid,
            'username' => $login->username,
        ];
    }

    /**
     * Returns a UserInterface object based on the credentials.
     *
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return \OcLegacy\User\LegacyUser|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (!$credentials['id'] || !$credentials['username']) {
            return null;
        }

        return new LegacyUser($credentials['id'], $credentials['username']);
    }

    /**
     * Returns true if the credentials are valid.
     *
     * @param mixed $credentials
     * @param UserInterface $user
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case

        // return true to cause authentication success
        return true;
    }

    /**
     * Called when the authentication is successful.
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     *
     * @return null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }

    /**
     * Called when the authentication fails.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return null;
    }

    /**
     * Called when authentication is needed, but it's not sent
     *
     * @param Request $request
     * @param AuthenticationException|null $authException
     *
     * @return RedirectResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $currentUri = $request->getUri();
        $url = '/login.php?target=' . rawurlencode($currentUri);

        return new RedirectResponse($url);
    }

    /**
     * @return bool
     */
    public function supportsRememberMe()
    {
        return false;
    }
}
