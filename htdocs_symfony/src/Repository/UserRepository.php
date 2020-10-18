<?php

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Oc\Entity\UserEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class UserRepository
{
    /**
     * Database table name that this repository maintains.
     *
     * @var string
     */
    public const TABLE = 'user';

    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var SecurityRolesRepository
     */
    private $securityRolesRepository;

    public function __construct(Connection $connection, SecurityRolesRepository  $securityRolesRepository)
    {
        $this->connection = $connection;
        $this->securityRolesRepository = $securityRolesRepository;
    }

    /**
     * Fetches all users.
     *
     * @throws RecordsNotFoundException Thrown when no records are found
     * @return UserEntity[]
     */
    public function fetchAll(): array
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->execute();

        $result = $statement->fetchAll();

        if ($statement->rowCount() === 0) {
            throw new RecordsNotFoundException('No records found');
        }

        return $this->getEntityArrayFromDatabaseArray($result);
    }

    /**
     * Fetches a user by its id.
     *
     * @throws RecordNotFoundException Thrown when the request record is not found
     */
    public function fetchOneById(int $id): UserEntity
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('user_id = :id')
            ->setParameter(':id', $id)
            ->execute();

        $result = $statement->fetch();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException(sprintf(
                'Record with id #%s not found',
                $id
            ));
        }

        return $this->getEntityFromDatabaseArray($result);
    }

    /**
     * Fetches a user by its username.
     *
     * @throws RecordNotFoundException Thrown when the request record is not found
     */
    public function fetchOneByUsername(string $username): UserEntity
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('username = :username')
            ->setParameter(':username', $username)
            ->execute();

        $result = $statement->fetch();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException(sprintf(
                'Record with username "%s" not found',
                $username
            ));
        }

        $user = $this->getEntityFromDatabaseArray($result);

        $user->roles = $this->securityRolesRepository->fetchUserRoles($user);

        return $user;
    }

    /**
     * Creates a user in the database.
     *
     * @throws RecordAlreadyExistsException
     */
    public function create(UserEntity $entity): UserEntity
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The user entity already exists');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->id = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * Update a user in the database.
     *
     * @throws RecordNotPersistedException
     */
    public function update(UserEntity $entity): UserEntity
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['id' => $entity->id]
        );

        $entity->id = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * Removes a user from the database.
     *
     * @throws RecordNotPersistedException
     */
    public function remove(UserEntity $entity): UserEntity
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['id' => $entity->id]
        );

        $entity->id = null;

        return $entity;
    }

    /**
     * Converts database array to entity array.
     *
     * @return UserEntity[]
     */
    private function getEntityArrayFromDatabaseArray(array $result): array
    {
        $languages = [];

        foreach ($result as $item) {
            $languages[] = $this->getEntityFromDatabaseArray($item);
        }

        return $languages;
    }

    /**
     * Maps the given entity to the database array.
     */
    public function getDatabaseArrayFromEntity(UserEntity $entity): array
    {
        return [
            'user_id' => $entity->id,
            'username' => $entity->username,
            'password' => $entity->password,
            'email' => $entity->email,
            'latitude' => $entity->latitude,
            'longitude' => $entity->longitude,
            'is_active_flag' => $entity->isActive,
            'first_name' => $entity->firstname,
            'last_name' => $entity->lastname,
            'country' => $entity->country,
            'language' => $entity->language,
        ];
    }

    /**
     * Prepares database array from properties.
     */
    public function getEntityFromDatabaseArray(array $data): UserEntity
    {
        $entity = new UserEntity();
        $entity->id = (int) $data['user_id'];
        $entity->username = $data['username'];
        $entity->password = $data['password'];
        $entity->email = $data['email'];
        $entity->latitude = (double) $data['latitude'];
        $entity->longitude = (double) $data['longitude'];
        $entity->isActive = (bool) $data['is_active_flag'];
        $entity->firstname = $data['first_name'];
        $entity->lastname = $data['last_name'];
        $entity->country = $data['country'];
        $entity->language = strtolower($data['language']);

        return $entity;
    }
}
