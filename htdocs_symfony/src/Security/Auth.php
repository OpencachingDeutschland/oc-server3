<?php

declare(strict_types=1);

namespace Oc\Security;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * Simple auth service — replaces Symfony Security.
 *
 * Reads the legacy ocdevelopmentdata cookie, validates against sys_sessions,
 * loads the user array. No ORM, no entities, no firewall.
 */
class Auth
{
    private ?array $user = null;
    private bool $loaded = false;

    public function __construct(
        private Connection $connection,
        private RequestStack $requestStack,
    ) {}

    /** Get the authenticated user as an array, or null. */
    public function getUser(): ?array
    {
        if (!$this->loaded) {
            $this->loadFromCookie();
        }
        return $this->user;
    }

    /** Get the user ID, or 0 if not authenticated. */
    public function getUserId(): int
    {
        $user = $this->getUser();
        return $user ? (int) $user['user_id'] : 0;
    }

    /** Check if the current user has the given role. */
    public function isGranted(string $role): bool
    {
        $user = $this->getUser();
        if (!$user) {
            return false;
        }

        // ROLE_USER is granted to anyone authenticated
        if ($role === 'ROLE_USER') {
            return true;
        }

        // Check user_roles table
        $result = $this->connection->createQueryBuilder()
            ->select('sr.role')
            ->from('user_roles', 'ur')
            ->join('ur', 'security_roles', 'sr', 'ur.role_id = sr.id')
            ->where('ur.user_id = :uid')
            ->setParameter('uid', $user['user_id'])
            ->executeQuery()
            ->fetchAllAssociative();

        foreach ($result as $row) {
            if ($row['role'] === $role) {
                return true;
            }
        }

        return false;
    }

    /** Login with username + password. Returns user array on success, null on failure. */
    public function login(string $username, string $password): ?array
    {
        $row = $this->connection->createQueryBuilder()
            ->select('*')->from('user')
            ->where('username = :u')
            ->setParameter('u', $username)
            ->executeQuery()
            ->fetchAssociative();

        if (!$row) {
            return null;
        }

        // Legacy MD5 password check
        if (md5($password) !== $row['password']) {
            return null;
        }

        // Create session in sys_sessions
        $uuid = $this->generateUuid();
        $now = date('Y-m-d H:i:s');

        $this->connection->insert('sys_sessions', [
            'uuid'       => $uuid,
            'user_id'    => $row['user_id'],
            'permanent'  => 0,
            'last_login' => $now,
        ]);

        // Build cookie data
        $cookieData = base64_encode(json_encode([
            'userid'    => $row['user_id'],
            'username'  => $row['username'],
            'sessionid' => $uuid,
            'permanent' => 0,
            'lastlogin' => $now,
        ]));

        $this->loginCookie = new \Symfony\Component\HttpFoundation\Cookie(
            'ocdevelopmentdata',
            $cookieData,
            time() + 365 * 86400,
            '/',
            null,
            true,   // secure
            true,   // httpOnly
            false,  // raw
            'lax'
        );

        $this->user = $row;
        $this->loaded = true;

        return $row;
    }

    /** Get the cookie to set after successful login. */
    public function getLoginCookie(): ?\Symfony\Component\HttpFoundation\Cookie
    {
        return $this->loginCookie ?? null;
    }

    private ?\Symfony\Component\HttpFoundation\Cookie $loginCookie = null;

    /** Logout — clear the cookie. */
    public function logout(): void
    {
        $this->user = null;
        $this->loaded = true;
    }

    private function loadFromCookie(): void
    {
        $this->loaded = true;

        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return;
        }

        $cookie = $request->cookies->get('ocdevelopmentdata');
        if (!$cookie) {
            return;
        }

        $data = json_decode(base64_decode($cookie), true);
        if (!$data || empty($data['userid']) || empty($data['sessionid'])) {
            return;
        }

        $userId    = (int) $data['userid'];
        $sessionId = $data['sessionid'];

        // Validate session
        $valid = $this->connection->createQueryBuilder()
            ->select('s.user_id')
            ->from('sys_sessions', 's')
            ->join('s', 'user', 'u', 's.user_id = u.user_id')
            ->where('s.uuid = :uuid')
            ->andWhere('u.is_active_flag = 1')
            ->setParameter('uuid', $sessionId)
            ->executeQuery()
            ->fetchOne();

        if (!$valid) {
            return;
        }

        // Load user
        $this->user = $this->connection->createQueryBuilder()
            ->select('*')->from('user')
            ->where('user_id = :uid')
            ->setParameter('uid', $userId)
            ->executeQuery()
            ->fetchAssociative() ?: null;
    }

    private function generateUuid(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
