<?php

namespace OcTest\Modules\Oc\User;

use Oc\Session\SessionDataInterface;
use Oc\User\UserEntity;
use Oc\User\UserProvider;
use Oc\User\UserService;
use OcTest\Modules\TestCase;

/**
 * Class UserProviderTest
 */
class UserProviderTest extends TestCase
{
    /**
     * Tests fetching the user by session.
     */
    public function testFetchingUserBySessionSuccess(): void
    {
        $user = new UserEntity();

        $sessionMock = $this->createMock(SessionDataInterface::class);
        $sessionMock->method('get')
            ->with('userid')
            ->willReturn(1);

        $userServiceMock = $this->createMock(UserService::class);
        $userServiceMock->method('fetchOneById')
            ->with(1)
            ->willReturn($user);

        $provider = new UserProvider($sessionMock, $userServiceMock);

        $result = $provider->bySession();

        self::assertSame($user, $result);
    }

    /**
     * Tests fetching the user by session when no userId is in session.
     */
    public function testFetchingUserBySessionNoUserId(): void
    {
        $sessionMock = $this->createMock(SessionDataInterface::class);
        $sessionMock->method('get')
                    ->with('userid')
                    ->willReturn(null);

        $userServiceMock = $this->createMock(UserService::class);

        $provider = new UserProvider($sessionMock, $userServiceMock);

        self::assertNull($provider->bySession());
    }

    /**
     * Tests fetching the user by session.
     */
    public function testFetchingUserBySessionNoUserFound(): void
    {
        $sessionMock = $this->createMock(SessionDataInterface::class);
        $sessionMock->method('get')
                    ->with('userid')
                    ->willReturn(1);

        $userServiceMock = $this->createMock(UserService::class);
        $userServiceMock->method('fetchOneById')
                        ->with(1)
                        ->willReturn(null);

        $provider = new UserProvider($sessionMock, $userServiceMock);

        self::assertNull($provider->bySession());
    }
}
