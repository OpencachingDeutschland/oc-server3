<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Oc\Entity\SupportListingCommentsEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

/**
 * Class SupportListingCommentsRepository
 *
 * @package Oc\Repository
 */
class SupportListingCommentsRepository
{
    const TABLE = 'support_listing_comments';

    /** @var Connection */
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return array
     * @throws RecordsNotFoundException
     * @throws \Exception
     */
    public function fetchAll()
    : array
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->executeQuery();

        $result = $statement->fetchAllAssociative();

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
     * @return SupportListingCommentsEntity
     * @throws RecordNotFoundException
     * @throws \Exception
     */
    public function fetchOneBy(array $where = [])
    : SupportListingCommentsEntity {
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
     * @param array $where
     *
     * @return array
     * @throws RecordsNotFoundException
     * @throws \Exception
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

        $statement = $queryBuilder->executeQuery();

        $result = $statement->fetchAllAssociative();

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
     * @param SupportListingCommentsEntity $entity
     *
     * @return SupportListingCommentsEntity
     * @throws RecordAlreadyExistsException
     * @throws Exception
     */
    public function create(SupportListingCommentsEntity $entity)
    : SupportListingCommentsEntity {
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
     * @param SupportListingCommentsEntity $entity
     *
     * @return SupportListingCommentsEntity
     * @throws RecordNotPersistedException
     * @throws Exception
     */
    public function update(SupportListingCommentsEntity $entity)
    : SupportListingCommentsEntity {
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
     * @param SupportListingCommentsEntity $entity
     *
     * @return SupportListingCommentsEntity
     * @throws RecordNotPersistedException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function remove(SupportListingCommentsEntity $entity)
    : SupportListingCommentsEntity {
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
     * @param SupportListingCommentsEntity $entity
     *
     * @return array
     */
    public function getDatabaseArrayFromEntity(SupportListingCommentsEntity $entity)
    : array {
        return [
            'id' => $entity->id,
            'wp_oc' => $entity->wpOc,
            'comment' => $entity->comment,
            'comment_created' => $entity->commentCreated,
            'comment_last_modified' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * @param array $data
     *
     * @return SupportListingCommentsEntity
     * @throws \Exception
     */
    public function getEntityFromDatabaseArray(array $data)
    : SupportListingCommentsEntity {
        $entity = new SupportListingCommentsEntity('');
        $entity->id = (int) $data['id'];
        $entity->wpOc = (string) $data['wp_oc'];
        $entity->comment = (string) $data['comment'];
        $entity->commentCreated = (string) $data['comment_created'];
        $entity->commentLastModified = (string) $data['comment_last_modified'];

        return $entity;
    }
}
