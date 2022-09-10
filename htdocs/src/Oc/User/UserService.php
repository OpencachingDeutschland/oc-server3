<?php

namespace Oc\User;

use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

/**
 *
 */
class UserService
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Fetches all users.
     *
     * @return UserEntity[]
     */
    public function fetchAll(): array
    {
        try {
            $result = $this->userRepository->fetchAll();
        } catch (RecordsNotFoundException $e) {
            $result = [];
        }

        return $result;
    }

    /**
     * Fetches a user by its id.
     */
    public function fetchOneById(int $id): ?UserEntity
    {
        try {
            $result = $this->userRepository->fetchOneById($id);
        } catch (RecordNotFoundException $e) {
            $result = null;
        }

        return $result;
    }

    /**
     * Creates a user in the database.
     *
     * @throws RecordAlreadyExistsException
*/
    public function create(UserEntity $entity): UserEntity
    {
        return $this->userRepository->create($entity);
    }

    /**
     * Update a user in the database.
     *
     * @throws RecordNotPersistedException
*/
    public function update(UserEntity $entity): UserEntity
    {
        return $this->userRepository->update($entity);
    }

    /**
     * Removes a user from the database.
     *
     * @throws RecordNotPersistedException
     */
    public function remove(UserEntity $entity): UserEntity
    {
        return $this->userRepository->remove($entity);
    }
}
