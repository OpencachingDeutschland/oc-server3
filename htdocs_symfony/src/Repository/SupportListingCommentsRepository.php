<?php

declare(strict_types=1);

namespace Oc\Repository;

use DateTime;
use Doctrine\DBAL\Connection;
use Oc\Entity\SupportListingInfosEntity;
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
     * @return SupportListingInfosEntity
     * @throws RecordNotFoundException
     */
    public function fetchOneBy(array $where = [])
    : SupportListingInfosEntity {
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
     * @param SupportListingInfosEntity $entity
     *
     * @return SupportListingInfosEntity
     * @throws RecordAlreadyExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function create(SupportListingInfosEntity $entity)
    : SupportListingInfosEntity {
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
     * @param SupportListingInfosEntity $entity
     *
     * @return SupportListingInfosEntity
     * @throws RecordNotPersistedException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function update(SupportListingInfosEntity $entity)
    : SupportListingInfosEntity {
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
     * @param SupportListingInfosEntity $entity
     *
     * @return SupportListingInfosEntity
     * @throws RecordNotPersistedException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function remove(SupportListingInfosEntity $entity)
    : SupportListingInfosEntity {
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
     * @param SupportListingInfosEntity $entity
     *
     * @return array
     */
    public function getDatabaseArrayFromEntity(SupportListingInfosEntity $entity)
    : array {
        return [
            'id' => $entity->id,
            'wp_oc' => $entity->wpOc,
            'comment' => $entity->comment,
            'comment_created' => $entity->commentCreated,
            'comment_created_by' => $entity->commentCreatedBy,
            'comment_last_modified' => $entity->commentLastModified,
        ];
    }

    /**
     * @param array $data
     *
     * @return SupportListingInfosEntity
     * @throws \Exception
     */
    public function getEntityFromDatabaseArray(array $data)
    : SupportListingInfosEntity {
        $entity = new SupportListingInfosEntity();
        $entity->id = (int) $data['id'];
        $entity->wpOc = (string) $data['wp_oc'];
        $entity->comment = (string) $data['comment'];
        $entity->commentCreated = new DateTime($data['comment_created']);
        $entity->commentCreatedBy = (string) $data['comment_created_by'];
        $entity->commentLastModified = new DateTime($data['comment_last_modified']);

        return $entity;
    }
}
