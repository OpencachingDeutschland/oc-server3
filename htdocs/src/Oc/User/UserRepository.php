<?php

namespace Oc\User;

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

/**
 * Class UserRepository
 *
 * @package Oc\User
 */
class UserRepository
{
    /**
     * Database table name that this repository maintains.
     *
     * @var string
     */
    const TABLE = 'user';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * UserRepository constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Fetches all users.
     *
     * @return UserEntity[]
     *
     * @throws RecordsNotFoundException Thrown when no records are found
     */
    public function fetchAll()
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
     * @param int $id
     *
     * @return UserEntity
     *
     * @throws RecordNotFoundException Thrown when the request record is not found
     */
    public function fetchOneById($id)
    {
        return $this->fetchOneBy([
            'user_id' => $id
        ]);
    }

    /**
     * Fetches a user by given where array.
     *
     * @param array $where
     *
     * @return UserEntity|null
     *
     * @throws RecordNotFoundException Thrown when no record is found
     */
    public function fetchOneBy(array $where = [])
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->setMaxResults(1);


        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->andWhere($column . ' = ' .  $queryBuilder->createNamedParameter($value));
            }
        }

        $statement = $queryBuilder->execute();

        $result = $statement->fetch();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException('Record with given where clause not found');
        }

        return $this->getEntityFromDatabaseArray($result);
    }

    /**
     * Creates a user in the database.
     *
     * @param UserEntity $entity
     *
     * @return UserEntity
     *
     * @throws RecordAlreadyExistsException
     */
    public function create(UserEntity $entity)
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
     * @param UserEntity $entity
     *
     * @return UserEntity
     *
     * @throws RecordNotPersistedException
     */
    public function update(UserEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['user_id' => $entity->id]
        );

        $entity->id = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * Removes a user from the database.
     *
     * @param UserEntity $entity
     *
     * @return UserEntity
     *
     * @throws RecordNotPersistedException
     */
    public function remove(UserEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->delete(
            self::TABLE,
            $databaseArray,
            ['user_id' => $entity->id]
        );

        $entity->id = null;

        return $entity;
    }

    /**
     * Converts database array to entity array.
     *
     * @param array $result
     *
     * @return UserEntity[]
     */
    private function getEntityArrayFromDatabaseArray(array $result)
    {
        $languages = [];

        foreach ($result as $item) {
            $languages[] = $this->getEntityFromDatabaseArray($item);
        }

        return $languages;
    }

    /**
     * Maps the given entity to the database array.
     *
     * @param UserEntity $entity
     *
     * @return array
     */
    public function getDatabaseArrayFromEntity(UserEntity $entity)
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
     *
     * @param array $data
     *
     * @return UserEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new UserEntity();
        $entity->id = (int) $data['user_id'];
        $entity->username = (string) $data['username'];
        $entity->password = (string) $data['password'];
        $entity->email = (string) $data['email'];
        $entity->latitude = (double) $data['latitude'];
        $entity->longitude = (double) $data['longitude'];
        $entity->isActive = (bool) $data['is_active_flag'];
        $entity->firstname = (string) $data['first_name'];
        $entity->lastname = (string) $data['last_name'];
        $entity->country = (string) $data['country'];
        $entity->language = strtolower($data['language']);

        return $entity;
    }
}
