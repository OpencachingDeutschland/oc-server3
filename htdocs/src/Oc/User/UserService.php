<?php

namespace Oc\User;

use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordsNotFoundException;

/**
 * Class UserService
 *
 * @package Oc\User
 */
class UserService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UserService constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Fetches all users.
     *
     * @return UserEntity[]
     */
    public function fetchAll()
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
     *
     * @param int $id
     *
     * @return null|UserEntity
     */
    public function fetchOneById($id)
    {
        try {
            $result = $this->userRepository->fetchOneById($id);
        } catch (RecordNotFoundException $e) {
            $result = null;
        }

        return $result;
    }

    public function fetchOneBy(array $where)
    {
        try {
            $result = $this->userRepository->fetchOneBy($where);
        } catch (RecordNotFoundException $e) {
            $result = null;
        }

        return $result;
    }

    /**
     * Creates a user in the database.
     *
     * @param UserEntity $entity
     *
     * @return UserEntity
     */
    public function create(UserEntity $entity)
    {
        return $this->userRepository->create($entity);
    }

    /**
     * Update a user in the database.
     *
     * @param UserEntity $entity
     *
     * @return UserEntity
     */
    public function update(UserEntity $entity)
    {
        return $this->userRepository->update($entity);
    }

    /**
     * Removes a user from the database.
     *
     * @param UserEntity $entity
     *
     * @return UserEntity
     */
    public function remove(UserEntity $entity)
    {
        return $this->userRepository->remove($entity);
    }
}
