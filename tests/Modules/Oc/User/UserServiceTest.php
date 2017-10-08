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
 *
 * @package OcTest\Modules\Oc\User
 */
class UserServiceTest extends TestCase
{
    /**
     * Tests fetching all records with success - no exception is thrown.
     *
     * @return void
     */
    public function testFetchingAllReturnsArrayWithUserEntities()
    {
        $userEntityArray = [
            new UserEntity(),
            new UserEntity()
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
     *
     * @return void
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
     *
     * @return void
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
     *
     * @return void
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
     * Tests fetching one record by with success - no exception is thrown.
     *
     * @return void
     */
    public function testFetchingOneByReturnsEntity()
    {
        $userEntity = new UserEntity();

        $whereClause = [
            'username' => '__foobar__'
        ];

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('fetchOneBy')
            ->with($whereClause)
            ->willReturn($userEntity);

        $userService = new UserService($userRepository);

        $result = $userService->fetchOneBy($whereClause);

        self::assertSame($userEntity, $result);
    }

    /**
     * Tests fetching one record by - exception is thrown because there is no record.
     *
     * @return void
     */
    public function testFetchingOneByThrowsException()
    {
        $exception = new RecordNotFoundException('Record with id #1 not found');

        $whereClause = [
            'username' => '__foobar__'
        ];

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('fetchOneBy')
            ->with($whereClause)
            ->willThrowException($exception);

        $userService = new UserService($userRepository);

        $result = $userService->fetchOneBy($whereClause);

        self::assertNull($result);
    }

    /**
     * Tests that create returns the entity.
     *
     * @return void
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
     *
     * @return void
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
     *
     * @return void
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
