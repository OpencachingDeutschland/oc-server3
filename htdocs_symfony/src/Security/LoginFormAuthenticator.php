<?php

declare(strict_types=1);

namespace Oc\Security;

use Exception;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\UserLoginBlockRepository;
use Oc\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Security;
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

    private CsrfTokenManagerInterface $csrfTokenManager;

    private Security $security;

    private UrlGeneratorInterface $urlGenerator;

    private UserLoginBlockRepository $userLoginBlockRepository;

    private UserPasswordHasherInterface $passwordEncoder;

    private UserRepository $userRepository;

    public function __construct(
            Security $security,
            UserLoginBlockRepository $userLoginBlockRepository,
            UserRepository $userRepository,
            UrlGeneratorInterface $urlGenerator,
            CsrfTokenManagerInterface $csrfTokenManager,
            UserPasswordHasherInterface $passwordEncoder
    ) {
        $this->security = $security;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->urlGenerator = $urlGenerator;
        $this->userLoginBlockRepository = $userLoginBlockRepository;
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
     * @throws Exception
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $firewallName): RedirectResponse
    {
        try {
            // If there's a login block found in database then route to special landing page
            $userLoginBlock = $this->userLoginBlockRepository->fetchOneBy(['user_id' => $this->security->getUser()->userId]);

            return new RedirectResponse(
                    $this->urlGenerator->generate('app_user_login_block')
            );
        } catch (RecordNotFoundException $e) {
            if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
                return new RedirectResponse($targetPath);
            } else {
                return new RedirectResponse($this->urlGenerator->generate('app_index_index'));
            }
        }
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}

