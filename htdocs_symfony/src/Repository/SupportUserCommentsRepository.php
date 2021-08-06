<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Oc\Entity\SupportUserCommentsEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

/**
 * Class SupportUserCommentsRepository
 *
 * @package Oc\Repository
 */
class SupportUserCommentsRepository
{
    const TABLE = 'support_listing_comments';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return array
     * @throws RecordsNotFoundException
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

        $records = [];

        foreach ($result as $item) {
            $records[] = $this->getEntityFromDatabaseArray($item);
        }

        return $records;
    }

    /**
     * @param array $where
     *
     * @return SupportUserCommentsEntity
     * @throws RecordNotFoundException
     */
    public function fetchOneBy(array $where = [])
    : SupportUserCommentsEntity {
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
     * @param array $where
     *
     * @return array
     * @throws RecordsNotFoundException
     */
    public function fetchBy(array $where = [])
    : array {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE);

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->andWhere($column . ' = ' . $queryBuilder->createNamedParameter($value));
            }
        }

        $statement = $queryBuilder->execute();

        $result = $statement->fetchAll();

        if ($statement->rowCount() === 0) {
            throw new RecordsNotFoundException('No records with given where clause found');
        }

        $entities = [];

        foreach ($result as $item) {
            $entities[] = $this->getEntityFromDatabaseArray($item);
        }

        return $entities;
    }

    /**
     * @param SupportUserCommentsEntity $entity
     *
     * @return SupportUserCommentsEntity
     * @throws RecordAlreadyExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function create(SupportUserCommentsEntity $entity)
    : SupportUserCommentsEntity {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
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
     * @param SupportUserCommentsEntity $entity
     *
     * @return SupportUserCommentsEntity
     * @throws RecordNotPersistedException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function update(SupportUserCommentsEntity $entity)
    : SupportUserCommentsEntity {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['id' => $entity->id]
        );

        return $entity;
    }

    /**
     * @param SupportUserCommentsEntity $entity
     *
     * @return SupportUserCommentsEntity
     * @throws RecordNotPersistedException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function remove(SupportUserCommentsEntity $entity)
    : SupportUserCommentsEntity {
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
     * @param SupportUserCommentsEntity $entity
     *
     * @return array
     */
    public function getDatabaseArrayFromEntity(SupportUserCommentsEntity $entity)
    : array {
        return [
            'id' => $entity->id,
            'oc_user_id' => $entity->ocUserId,
            'comment' => $entity->comment,
            'comment_created' => $entity->commentCreated,
            'comment_created_by' => $entity->commentCreatedBy,
            'lastmodified' => $entity->commentLastModified,
        ];
    }

    /**
     * @param array $data
     *
     * @return SupportUserCommentsEntity
     * @throws \Exception
     */
    public function getEntityFromDatabaseArray(array $data)
    : SupportUserCommentsEntity {
        $entity = new SupportUserCommentsEntity();
        $entity->id = (int) $data['id'];
        $entity->ocUserId = (int) $data['oc_user_id'];
        $entity->comment = (string) $data['comment'];
        $entity->commentCreated = new DateTime($data['comment_created']);
        $entity->commentCreatedBy = (string) $data['comment_created_by'];
        $entity->commentLastModified = new DateTime($data['lastmodified']);

        return $entity;
    }
}
