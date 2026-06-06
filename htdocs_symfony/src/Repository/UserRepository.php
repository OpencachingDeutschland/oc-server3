<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Oc\Entity\UserEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class UserRepository
{
    public const TABLE = 'user';

    private Connection $connection;

    private SecurityRolesRepository $securityRolesRepository;

    public function __construct(Connection $connection, SecurityRolesRepository $securityRolesRepository)
    {
        $this->connection = $connection;
        $this->securityRolesRepository = $securityRolesRepository;
    }

    /**
     * Fetches all users.
     *
     * @throws Exception
     * @throws RecordsNotFoundException Thrown when no records are found
     */
    public function fetchAll(): array
    {
        $statement = $this->connection->createQueryBuilder()
                ->select('*')
                ->from(self::TABLE)
                ->executeQuery();

        $result = $statement->fetchAllAssociative();

        if ($statement->rowCount() === 0) {
            throw new RecordsNotFoundException('No records found');
        }

        return $this->getEntityArrayFromDatabaseArray($result);
    }

    /**
     * @throws Exception
     * @throws RecordNotFoundException
     */
    public function fetchOneBy(array $where = []): UserEntity
    {
        $queryBuilder = $this->connection->createQueryBuilder()
                ->select('*')
                ->from(self::TABLE)
                ->setMaxResults(1);

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->andWhere($column . ' = ' . $queryBuilder->createNamedParameter($value));
            }
        }

        $statement = $queryBuilder->executeQuery();

        $result = $statement->fetchAssociative();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException('Record with given where clause not found');
        }

        return $this->getEntityFromDatabaseArray($result);
    }

    /**
     * Fetches a user by its id.
     *
     * @throws Exception
     * @throws RecordNotFoundException Thrown when the request record is not found
     */
    public function fetchOneById(int $id): UserEntity
    {
        $statement = $this->connection->createQueryBuilder()
                ->select('*')
                ->from(self::TABLE)
                ->where('user_id = :id')
                ->setParameter('id', $id)
                ->executeQuery();

        $result = $statement->fetchAssociative();

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
     * @throws Exception
     * @throws RecordNotFoundException
     */
    public function fetchOneByUsername(string $username): UserEntity
    {
        $statement = $this->connection->createQueryBuilder()
                ->select('*')
                ->from(self::TABLE)
                ->where('username = :username')
                ->setParameter('username', $username)
                ->executeQuery();

        $result = $statement->fetchAssociative();

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
     * @throws RecordAlreadyExistsException
     * @throws Exception
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

        $entity->userId = (int)$this->connection->lastInsertId();

        return $entity;
    }

    /**
     * Update a user in the database.
     *
     * @throws RecordNotPersistedException
     * @throws Exception
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
                ['user_id' => $entity->userId]
        );

        $entity->userId = (int)$this->connection->lastInsertId();

        return $entity;
    }

    /**
     * Removes a user from the database.
     *
     * @throws RecordNotPersistedException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function remove(UserEntity $entity): UserEntity
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
                self::TABLE,
                ['user_id' => $entity->userId]
        );

        $entity->userId = 0;

        return $entity;
    }

    /**
     * generate an activation code (e.g. for user registration)
     */
    public function generateActivationCode(): string
    {
        return mb_strtoupper(mb_substr(md5(uniqid('', true)), 0, 13));
    }

    /**
     * Converts database array to entity array.
     *
     * @throws Exception
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
                'user_id' => $entity->userId,
                'date_created' => $entity->dateCreated,
                'last_modified' => $entity->lastModified,
                'last_login' => $entity->lastLogin,
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
                'permanent_login_flag' => $entity->permanentLoginFlag,
                'activation_code' => $entity->activationCode,
                'language' => $entity->language,
                'description' => $entity->description,
                'gdpr_deletion' => $entity->gdprDeletion,
        ];
    }

    /**
     * Prepares database array from properties.
     *
     * @throws Exception
     * @throws \Exception
     */
    public function getEntityFromDatabaseArray(array $data): UserEntity
    {
        $entity = new UserEntity();
        $entity->userId = (int)$data['user_id'];
        $entity->dateCreated = $data['date_created'];
        $entity->lastModified = $data['last_modified'];
        $entity->lastLogin = (string)$data['last_login'];
        $entity->username = $data['username'];
        $entity->password = (string)$data['password'];
        $entity->email = (string)$data['email'];
        $entity->emailProblems = (bool)$data['email_problems'];
        $entity->latitude = (double)$data['latitude'];
        $entity->longitude = (double)$data['longitude'];
        $entity->isActive = (bool)$data['is_active_flag'];
        $entity->firstname = $data['first_name'];
        $entity->lastname = $data['last_name'];
        $entity->country = (string)$data['country'];
        $entity->permanentLoginFlag = (bool)$data['permanent_login_flag'];
        $entity->activationCode = $data['activation_code'];
        $entity->language = strtolower($data['language'] ?? 'en');
        $entity->description = $data['description'];
        $entity->gdprDeletion = (bool)$data['gdpr_deletion'];
        $entity->roles = $this->securityRolesRepository->fetchUserRoles($entity);

        return $entity;
    }

    /**
     * @throws Exception
     */
    public function getUsersForSearchField(string $searchtext): array
    {
        //        SELECT user_id, username
        //        FROM user
        //        WHERE user_id      =       "' . $searchtext . '"
        //        OR user.email      =       "' . $searchtext . '"
        //        OR user.username   LIKE    "%' . $searchtext . '%"'
        $qb = $this->connection->createQueryBuilder();
        $qb->select('user.user_id', 'user.username')
                ->from('user')
                ->where('user.user_id = :searchTerm')
                ->orWhere('user.email = :searchTerm')
                ->orWhere('user.username LIKE :searchTermLIKE')
                ->setParameters(['searchTerm' => $searchtext, 'searchTermLIKE' => '%' . $searchtext . '%'])
                ->orderBy('user.username', 'ASC');

        return $qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * @throws RecordNotFoundException
     */
    public function search_by_user_id(int $userID): UserEntity
    {
        $fetchedUser = [];

        try {
            $fetchedUser = $this->fetchOneById($userID);
        } catch (Exception $e) {
            //  tue was..
        }

        return $fetchedUser;
    }

    /** @throws Exception */
    public function searchUsers(string $query, bool $includeEmail): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->from('user', 'u')
            ->leftJoin('u', 'stat_user', 's', 'u.user_id = s.user_id')
            ->orderBy('u.username', 'ASC');

        if ($includeEmail) {
            $qb->select(
                'u.user_id', 'u.username', 'u.email', 'u.date_created',
                'IFNULL(s.found, 0) AS find_count',
                'IFNULL(s.hidden, 0) AS hide_count'
            )
               ->where('u.user_id = :exact')
               ->orWhere('u.email = :exact')
               ->orWhere('u.username LIKE :like')
               ->setMaxResults(200);
        } else {
            $qb->select(
                'u.user_id', 'u.username',
                'IFNULL(s.found, 0) AS find_count',
                'IFNULL(s.hidden, 0) AS hide_count'
            )
               ->where('u.username LIKE :like')
               ->setMaxResults(20);
        }

        return $qb->setParameters([
            'exact' => $query,
            'like'  => '%' . $query . '%',
        ])->executeQuery()->fetchAllAssociative();
    }

    /** @throws Exception */
    public function fetchUserStats(int $userId): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('IFNULL(found, 0) AS findCount', 'IFNULL(hidden, 0) AS hideCount')
            ->from('stat_user')
            ->where('user_id = :id')
            ->setParameter('id', $userId)
            ->executeQuery()
            ->fetchAssociative();

        return $result ?: ['findCount' => 0, 'hideCount' => 0];
    }

    /** @throws Exception */
    public function countActiveUsers(): int
    {
        return (int)$this->connection->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('user')
            ->where('is_active_flag = 1')
            ->executeQuery()
            ->fetchOne();
    }

    /** @throws Exception */
    public function fetchOwnerStats(int $ownerId): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('IFNULL(found, 0) AS found', 'IFNULL(hidden, 0) AS hidden')
            ->from('stat_user')
            ->where('user_id = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->executeQuery()
            ->fetchAssociative();

        return $result ?: ['found' => 0, 'hidden' => 0];
    }
}
