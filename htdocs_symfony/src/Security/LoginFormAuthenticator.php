<?php

declare(strict_types=1);

namespace Oc\Security;

use Exception;
use Doctrine\DBAL\Connection;
use Oc\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Bundle\SecurityBundle\Security;
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

    private UserPasswordHasherInterface $passwordEncoder;

    private UserRepository $userRepository;

    public function __construct(
            private Connection $connection,
            Security $security,
            UserRepository $userRepository,
            UrlGeneratorInterface $urlGenerator,
            CsrfTokenManagerInterface $csrfTokenManager,
            UserPasswordHasherInterface $passwordEncoder
    ) {
        $this->security = $security;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->urlGenerator = $urlGenerator;
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
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): RedirectResponse
    {
        $userId = $this->security->getUser()->userId;
        $blocked = $this->connection->createQueryBuilder()
            ->select('user_id')->from('user_login_block')
            ->where('user_id = :uid')->setParameter('uid', $userId)
            ->executeQuery()->fetchOne();

        if ($blocked) {
            return new RedirectResponse(
                $this->urlGenerator->generate('app_user_login_block')
            );
        }

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

