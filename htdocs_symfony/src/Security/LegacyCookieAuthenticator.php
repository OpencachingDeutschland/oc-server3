<?php

declare(strict_types=1);

namespace Oc\Security;

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * Bridges the legacy ocserver-3 PHP session cookie into Symfony security.
 *
 * The legacy cookie stores a base64-encoded JSON blob under
 *   {COOKIE_PREFIX}data
 * containing userid / sessionid / permanent / lastlogin / username.
 *
 * We validate userid+sessionid against the sys_sessions table (same logic
 * as lib2/login.class.php::verify()) and, if valid, log the user in.
 */
class LegacyCookieAuthenticator extends AbstractAuthenticator
{
    private const COOKIE_PREFIX = 'ocdevelopment';
    private const COOKIE_NAME = self::COOKIE_PREFIX . 'data';

    private const LOGIN_TIME = 3600;                 // 1 hour
    private const LOGIN_TIME_PERMANENT = 7776000;    // 90 days

    public function __construct(
        private readonly Connection $connection,
        private readonly UserRepository $userRepository
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return $request->cookies->has(self::COOKIE_NAME)
            && $this->decode($request->cookies->get(self::COOKIE_NAME)) !== null;
    }

    public function authenticate(Request $request): Passport
    {
        $values = $this->decode($request->cookies->get(self::COOKIE_NAME));
        if ($values === null) {
            throw new CustomUserMessageAuthenticationException('Legacy session cookie malformed.');
        }

        $userId = (int) ($values['userid'] ?? 0);
        $sessionId = (string) ($values['sessionid'] ?? '');
        $permanent = ((int) ($values['permanent'] ?? 0)) === 1;

        if ($userId <= 0 || $sessionId === '') {
            throw new CustomUserMessageAuthenticationException('Legacy session cookie empty.');
        }

        $minLastLogin = date('Y-m-d H:i:s', time() - ($permanent ? self::LOGIN_TIME_PERMANENT : self::LOGIN_TIME));

        $row = $this->connection->createQueryBuilder()
            ->select('u.username')
            ->from('sys_sessions', 's')
            ->innerJoin('s', 'user', 'u', 'u.user_id = s.user_id')
            ->where('s.uuid = :uuid')
            ->andWhere('s.user_id = :uid')
            ->andWhere('u.is_active_flag = 1')
            ->andWhere('s.last_login > :since')
            ->setParameter('uuid', $sessionId)
            ->setParameter('uid', $userId)
            ->setParameter('since', $minLastLogin)
            ->executeQuery()
            ->fetchAssociative();

        if (!$row) {
            throw new CustomUserMessageAuthenticationException('Legacy session expired or invalid.');
        }

        $username = (string) $row['username'];

        return new SelfValidatingPassport(
            new UserBadge($username, function (string $identifier) {
                try {
                    return $this->userRepository->fetchOneByUsername($identifier);
                } catch (RecordNotFoundException $e) {
                    throw new UserNotFoundException('User not found.', 0, $e);
                }
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Let the request continue — legacy cookie carries the auth.
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        // Don't block the request — fall through so unauthenticated paths
        // (login form, public routes) still work.
        return null;
    }

    private function decode(?string $raw): ?array
    {
        if ($raw === null || $raw === '') {
            return null;
        }
        $decoded = base64_decode($raw, true);
        if ($decoded === false) {
            return null;
        }
        $values = json_decode($decoded, true);
        return is_array($values) ? $values : null;
    }
}
