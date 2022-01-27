<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Oc\Entity\UserEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

/**
 *
 */
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

    /**
     * @param Connection $connection
     * @param SecurityRolesRepository $securityRolesRepository
     */
    public function __construct(Connection $connection, SecurityRolesRepository $securityRolesRepository)
    {
        $this->connection = $connection;
        $this->securityRolesRepository = $securityRolesRepository;
    }

    /**
     * Fetches all users.
     *
     * @return UserEntity[]
     * @throws RecordsNotFoundException Thrown when no records are found
     */
    public function fetchAll()
    : array
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
     * @param array $where
     *
     * @return UserEntity
     * @throws RecordNotFoundException
     */
    public function fetchOneBy(array $where = [])
    : UserEntity {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->setMaxResults(1);

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->andWhere($column . ' = ' . $queryBuilder->createNamedParameter($value));
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
     * Fetches a user by its id.
     *
     * @throws RecordNotFoundException Thrown when the request record is not found
     */
    public function fetchOneById(int $id)
    : UserEntity {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('user_id = :id')
            ->setParameter(':id', $id)
            ->execute();

        $result = $statement->fetch();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException(
                sprintf(
                    'Record with id #%s not found',
                    $id
                )
            );
        }

        return $this->getEntityFromDatabaseArray($result);
    }

    /**
     * Fetches a user by its username.
     *
     * @param string $username
     *
     * @return UserEntity
     * @throws RecordNotFoundException
     */
    public function fetchOneByUsername(string $username)
    : UserEntity {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('username = :username')
            ->setParameter(':username', $username)
            ->execute();

        $result = $statement->fetch();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException(
                sprintf(
                    'Record with username "%s" not found',
                    $username
                )
            );
        }

        $user = $this->getEntityFromDatabaseArray($result);

        $user->roles = $this->securityRolesRepository->fetchUserRoles($user);

        return $user;
    }

    /**
     * Creates a user in the database.
     *
     * @param UserEntity $entity
     *
     * @return UserEntity
     * @throws RecordAlreadyExistsException
     * @throws DBALException
     */
    public function create(UserEntity $entity)
    : UserEntity {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The user entity already exists');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->userId = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * Update a user in the database.
     *
     * @param UserEntity $entity
     *
     * @return UserEntity
     * @throws RecordNotPersistedException
     * @throws DBALException
     */
    public function update(UserEntity $entity)
    : UserEntity {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['user_id' => $entity->userId]
        );

        $entity->userId = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * Removes a user from the database.
     *
     * @param UserEntity $entity
     *
     * @return UserEntity
     * @throws RecordNotPersistedException
     * @throws DBALException
     * @throws InvalidArgumentException
     */
    public function remove(UserEntity $entity)
    : UserEntity {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['user_id' => $entity->userId]
        );

        $entity->userId = null;

        return $entity;
    }

    /**
     * generate an activation code (e.g. for user registration)
     *
     * @return string
     */
    public function generateActivationCode() : string {
        return mb_strtoupper(mb_substr(md5(uniqid('', true)), 0, 13));
    }

    /**
     * Converts database array to entity array.
     *
     * @return UserEntity[]
     */
    private function getEntityArrayFromDatabaseArray(array $result)
    : array {
        $languages = [];

        foreach ($result as $item) {
            $languages[] = $this->getEntityFromDatabaseArray($item);
        }

        return $languages;
    }

    /**
     * Maps the given entity to the database array.
     */
    public function getDatabaseArrayFromEntity(UserEntity $entity)
    : array {
        return [
            'user_id' => $entity->userId,
            'date_created' => $entity->dateCreated,
            'last_modified' => $entity->lastModified,
            'username' => $entity->username,
            'password' => $entity->password,
            'email' => $entity->email,
            'email_problems' => $entity->emailProblems,
            'latitude' => $entity->latitude,
            'longitude' => $entity->longitude,
            'is_active_flag' => $entity->isActive,
            'first_name' => $entity->firstname,
            'last_name' => $entity->lastname,
            'country' => $entity->country,
            'activation_code' => $entity->activationCode,
            'language' => $entity->language,
            'description' => $entity->description,
            'gdpr_deletion' => $entity->gdprDeletion,
            'roles' => $entity->roles
        ];
    }

    /**
     * Prepares database array from properties.
     */
    public function getEntityFromDatabaseArray(array $data)
    : UserEntity {
        $entity = new UserEntity();
        $entity->userId = (int) $data['user_id'];
        $entity->dateCreated = (string) $data['date_created'];
        $entity->lastModified = (string) $data['last_modified'];
        $entity->username = $data['username'];
        $entity->password = $data['password'];
        $entity->email = $data['email'];
        $entity->emailProblems = (bool) $data['email_problems'];
        $entity->latitude = (double) $data['latitude'];
        $entity->longitude = (double) $data['longitude'];
        $entity->isActive = (bool) $data['is_active_flag'];
        $entity->firstname = $data['first_name'];
        $entity->lastname = $data['last_name'];
        $entity->country = $data['country'];
        $entity->activationCode = $data['activation_code'];
        $entity->language = strtolower($data['language']);
        $entity->description = $data['description'];
        $entity->gdprDeletion = $data['gdpr_deletion'];
        $entity->roles = $this->securityRolesRepository->fetchUserRoles($entity);

        return $entity;
    }
}
