<?php

namespace OcTest\Modules\Oc\User;

use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\User\UserEntity;
use Oc\User\UserRepository;
use OcTest\Modules\DBALConnectionTestCase;

/**
 * Class UserRepositoryTest
 *
 * @package OcTest\Modules\Oc\User
 */
class UserRepositoryTest extends DBALConnectionTestCase
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * Sets up test.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->repository = $this->container->get('oc.user.user_repository');
    }

    /**
     * Test fetching all records.
     *
     * @return void
     */
    public function testFetchAll()
    {
        $result = $this->repository->fetchAll();

        self::assertRepositoryFetchAll($result, UserEntity::class);
    }

    /**
     * Test fetching one record by given id.
     *
     * @return void
     */
    public function testFetchOneById()
    {
        // Fetch the current id of the root user
        $rootUser = $this->repository->fetchOneBy([
            'username' => 'root'
        ]);

        if ($rootUser === null) {
            self::markTestSkipped('User with username root not found');
        }

        $userId = $rootUser->id;

        $result = $this->repository->fetchOneById($userId);

        if ($result === null) {
            self::markTestSkipped(
                sprintf(
                    'User with id %s not found',
                    $userId
                )
            );
            return;
        }

        self::assertInstanceOf(UserEntity::class, $result);
        self::assertSame('root', $result->username);
    }

    /**
     * Test fetching record by given where clause that results into a non existing record.
     *
     * @return void
     */
    public function testFetchOneByIdNoneFound()
    {
        $this->setExpectedException(RecordNotFoundException::class);

        $this->repository->fetchOneById(0);
    }

    /**
     * Test fetching one record by given where clause.
     *
     * @return void
     */
    public function testFetchOneBy()
    {
        $result = $this->repository->fetchOneBy([
            'username' => 'root'
        ]);

        if ($result === null) {
            self::markTestSkipped('User with username root not found');
            return;
        }

        self::assertInstanceOf(UserEntity::class, $result);
        self::assertSame('root', $result->username);
    }

    /**
     * Test fetching record by given where clause that results into a non existing record.
     *
     * @return void
     */
    public function testFetchOneByNoneFound()
    {
        $this->setExpectedException(RecordNotFoundException::class);

        $this->repository->fetchOneBy([
            'username' => '__foobar__'
        ]);
    }

    /**
     * Test creating a record.
     *
     * @return void
     */
    public function testCreateUser()
    {
        $user = new UserEntity();
        $user->username = 'mmustermann';
        $user->firstname = 'Max';
        $user->lastname = 'Mustermann';
        $user->isActive = true;
        $user->longitude = 51.00;
        $user->latitude = 09.00;
        $user->language = 'de';
        $user->email = 'max@mustermann.de';

        $userResult = $this->repository->create($user);

        self::assertNotNull($userResult->id);
    }

    /**
     * Tests creating a record results in an error when the record entity id is set.
     *
     * @return void
     */
    public function testCreateUserThatAlreadyExists()
    {
        $this->setExpectedException(RecordAlreadyExistsException::class);

        $user = new UserEntity();
        $user->id = 1;

        $this->repository->create($user);
    }

    /**
     * Test updating record.
     *
     * @return void
     */
    public function testUpdatingUser()
    {
        $user = $this->repository->fetchOneBy([
            'username' => 'root'
        ]);

        if ($user === null) {
            self::markTestSkipped('User root cannot be found');
            return;
        }

        $user->firstname = 'Max';

        $userResult = $this->repository->update($user);

        self::assertNotNull($userResult->id);
        self::assertSame('Max', $userResult->firstname);
    }

    /**
     * Tests creating a user results in an error when the user entity id is set.
     *
     * @return void
     */
    public function testUpdateUserThatDoesNotExist()
    {
        $this->setExpectedException(RecordNotPersistedException::class);

        $user = new UserEntity();

        $this->repository->update($user);
    }

    /**
     * Test removing user.
     *
     * @return void
     */
    public function testRemoveUser()
    {
        $user = $this->repository->fetchOneBy([
            'username' => 'root'
        ]);

        if ($user === null) {
            self::markTestSkipped('User root cannot be found');
            return;
        }

        $userResult = $this->repository->remove($user);

        self::assertNull($userResult->id);
    }

    /**
     * Tests creating a user results in an error when the user entity id is set.
     *
     * @return void
     */
    public function testRemoveUserThatDoesNotExist()
    {
        $this->setExpectedException(RecordNotPersistedException::class);

        $user = new UserEntity();

        $this->repository->remove($user);
    }
}
