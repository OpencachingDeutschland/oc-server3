<?php

namespace OcTest\Modules\Oc\User;

use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordsNotFoundException;
use Oc\User\UserEntity;
use Oc\User\UserRepository;
use Oc\User\UserService;
use OcTest\Modules\TestCase;

/**
 * Class UserServiceTest
 */
class UserServiceTest extends TestCase
{
    /**
     * Tests fetching all records with success - no exception is thrown.
     */
    public function testFetchingAllReturnsArrayWithUserEntities()
    {
        $userEntityArray = [
            new UserEntity(),
            new UserEntity(),
        ];

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('fetchAll')
            ->willReturn($userEntityArray);

        $userService = new UserService($userRepository);

        $result = $userService->fetchAll();

        self::assertSame($userEntityArray, $result);
    }

    /**
     * Tests fetching all records - exception is thrown because there are no records.
     */
    public function testFetchingAllThrowsException()
    {
        $exception = new RecordsNotFoundException('No records found');

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('fetchAll')
            ->willThrowException($exception);

        $userService = new UserService($userRepository);

        $result = $userService->fetchAll();

        self::assertEmpty($result);
    }

    /**
     * Tests fetching one record by id with success - no exception is thrown.
     */
    public function testFetchingOneByIdReturnsEntity()
    {
        $userEntity = new UserEntity();

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('fetchOneById')
            ->with(1)
            ->willReturn($userEntity);

        $userService = new UserService($userRepository);

        $result = $userService->fetchOneById(1);

        self::assertSame($userEntity, $result);
    }

    /**
     * Tests fetching one record by id - exception is thrown because there is no record.
     */
    public function testFetchingOneByIdThrowsException()
    {
        $exception = new RecordNotFoundException('Record with id #1 not found');

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('fetchOneById')
            ->with(1)
            ->willThrowException($exception);

        $userService = new UserService($userRepository);

        $result = $userService->fetchOneById(1);

        self::assertNull($result);
    }

    /**
     * Tests that create returns the entity.
     */
    public function testCreateReturnsEntity()
    {
        $user = new UserEntity();

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('create')
            ->with($user)
            ->willReturn($user);

        $userService = new UserService($userRepository);

        $result = $userService->create($user);

        self::assertSame($user, $result);
    }

    /**
     * Tests that update returns the entity.
     */
    public function testUpdateReturnsEntity()
    {
        $user = new UserEntity();

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('update')
            ->with($user)
            ->willReturn($user);

        $userService = new UserService($userRepository);

        $result = $userService->update($user);

        self::assertSame($user, $result);
    }

    /**
     * Tests that remove returns the entity.
     */
    public function testRemoveReturnsEntity()
    {
        $user = new UserEntity();

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('remove')
            ->with($user)
            ->willReturn($user);

        $userService = new UserService($userRepository);

        $result = $userService->remove($user);

        self::assertSame($user, $result);
    }
}
