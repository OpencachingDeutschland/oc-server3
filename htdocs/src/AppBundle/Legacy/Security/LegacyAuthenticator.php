<?php

namespace AppBundle\Legacy\Security;

use AppBundle\Legacy\User\LegacyUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class LegacyAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * Called on every request. Return whatever credentials you want,
     * or null to stop authentication.
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

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (!$credentials['id']) {
            return null;
        }

        return new LegacyUser($credentials['id'], $credentials['username']);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case

        // return true to cause authentication success
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return null;
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $currentUri = $request->getUri();
        $url = '/login.php?target=' . rawurlencode($currentUri);

        return new RedirectResponse($url);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}