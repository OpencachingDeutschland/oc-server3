<?php

declare(strict_types=1);

namespace Oc\Security;

use Oc\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_security_login';

    private UrlGeneratorInterface $urlGenerator;

    private CsrfTokenManagerInterface $csrfTokenManager;

    private UserPasswordHasherInterface $passwordEncoder;

    private UserRepository $userRepository;

    public function __construct(
            UserRepository $userRepository,
            UrlGeneratorInterface $urlGenerator,
            CsrfTokenManagerInterface $csrfTokenManager,
            UserPasswordHasherInterface $passwordEncoder
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
    }

    public function authenticate(Request $request): Passport
    {
        $userName = $request->request->get('username');
        $password = $request->request->get('password');

        // https://symfonycasts.com/screencast/symfony6-upgrade/custom-authenticator
        return new Passport(
                new UserBadge($userName, function ($userIdentifier) {
                    // optionally pass a callback to load the User manually
                    $user = $this->userRepository->fetchOneBy(['username' => $userIdentifier]);
                    if (!$user) {
                        throw new UserNotFoundException();
                    }

                    return $user;
                }),
                new PasswordCredentials($password),
                [
                        new CsrfTokenBadge(
                                'authenticate',
                                $request->request->get('_csrf_token')
                        ),
                        (new RememberMeBadge())->enable(),
                ]
        );
    }

    /**
     * @param Request        $request
     * @param TokenInterface $token
     * @param                $firewallName
     *
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $firewallName): RedirectResponse
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_index_index'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}

