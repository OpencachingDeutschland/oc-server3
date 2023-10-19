<?php

declare(strict_types=1);

namespace Oc\Repository;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Oc\Entity\GeoCacheDescModifiedEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CacheDescModifiedRepository
{
    private const TABLE = 'cache_desc_modified';

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws RecordsNotFoundException
     * @throws \Exception
     */
    public function fetchAll(): array
    {
        $statement = $this->connection->createQueryBuilder()
                ->select('*')
                ->from(self::TABLE)
                ->executeQuery();

        $result = $statement->fetchAllAssociative();

//        if ($statement->rowCount() === 0) {
//            throw new RecordsNotFoundException('No records found');
//        }

        $records = [];

        foreach ($result as $item) {
            $records[] = $this->getEntityFromDatabaseArray($item);
        }

        return $records;
    }

    /**
     * @throws RecordNotFoundException
     * @throws \Exception
     */
    public function fetchOneBy(array $where = []): GeoCacheDescModifiedEntity
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

//        if ($statement->rowCount() === 0) {
//            throw new RecordNotFoundException('Record with given where clause not found');
//        }

        return $this->getEntityFromDatabaseArray($result);
    }

    /**
     * @throws RecordsNotFoundException
     * @throws \Exception
     */
    public function fetchBy(array $where = []): array
    {
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

//        if ($statement->rowCount() === 0) {
//            throw new RecordsNotFoundException('No records with given where clause found');
//        }

        $entities = [];

        foreach ($result as $item) {
            $entities[] = $this->getEntityFromDatabaseArray($item);
        }

        return $entities;
    }

    /**
     * @throws RecordAlreadyExistsException
     * @throws Exception
     */
    public function create(GeoCacheDescModifiedEntity $entity): GeoCacheDescModifiedEntity
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
                self::TABLE,
                $databaseArray
        );

        $entity->cacheId = (int)$this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @throws RecordNotPersistedException
     * @throws Exception
     */
    public function update(GeoCacheDescModifiedEntity $entity): GeoCacheDescModifiedEntity
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
                self::TABLE,
                $databaseArray,
                ['cache_id' => $entity->cacheId]
        );

        return $entity;
    }

    /**
     * @throws RecordNotPersistedException
     * @throws Exception
     */
    public function remove(GeoCacheDescModifiedEntity $entity): GeoCacheDescModifiedEntity
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
                self::TABLE,
                ['cache_id' => $entity->cacheId]
        );

        $entity->cacheId = 0;

        return $entity;
    }

    public function getDatabaseArrayFromEntity(GeoCacheDescModifiedEntity $entity): array
    {
        return [
                'cache_id' => $entity->cacheId,
                'language' => $entity->language,
                'date_modified' => $entity->dateModified,
                'date_created' => $entity->dateCreated,
                'desc' => $entity->desc,
                'desc_html' => $entity->descHtml,
                'desc_htmledit' => $entity->descHtmledit,
                'hint' => $entity->hint,
                'short_desc' => $entity->shortDesc,
                'restored_by' => $entity->restoredBy,
        ];
    }

    /**
     * @throws \Exception
     */
    public function getEntityFromDatabaseArray(array $data): GeoCacheDescModifiedEntity
    {
        $entity = new GeoCacheDescModifiedEntity();
        $entity->cacheId = (int)$data['cache_id'];
        $entity->language = (string)$data['language'];
        $entity->dateModified = new DateTime($data['date_modified']);
        $entity->dateCreated = new DateTime($data['date_created']);
        $entity->desc = (string)$data['desc'];
        $entity->descHtml = (int)$data['desc_html'];
        $entity->descHtmledit = (int)$data['desc_htmledit'];
        $entity->hint = (string)$data['hint'];
        $entity->shortDesc = (string)$data['short_desc'];
        $entity->restoredBy = (int)$data['restored_by'];

        return $entity;
    }
}
