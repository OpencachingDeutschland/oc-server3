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
 * Class SupportListingInfosRepository
 *
 * @package Oc\Repository
 */
class SupportListingInfosRepository
{
    const TABLE = 'support_listing_infos';

    /** @var Connection */
    private $connection;

    /** @var NodesRepository */
    private $nodesRepository;

    /**
     * @param Connection $connection
     * @param NodesRepository $nodesRepository
     */
    public function __construct(Connection $connection, NodesRepository $nodesRepository)
    {
        $this->connection = $connection;
        $this->nodesRepository = $nodesRepository;
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
            'node_id' => $entity->nodeId,
            'node_owner_id' => $entity->nodeOwnerId,
            'node_listing_id' => $entity->nodeListingId,
            'node_listing_wp' => $entity->nodeListingWP,
            'node_listing_name' => $entity->nodeListingName,
            'node_listing_size' => $entity->nodeListingSize,
            'node_listing_difficulty' => $entity->nodeListingDifficulty,
            'node_listing_terrain' => $entity->nodeListingTerrain,
            'node_listing_coordinates_lon' => $entity->nodeListingCoordinatesLon,
            'node_listing_coordinates_lat' => $entity->nodeListingCoordinatesLat,
            'node_listing_archived' => $entity->nodeListingArchived,
            'last_modified' => $entity->lastModified,
            'importstatus' => $entity->importStatus,
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
        $entity->nodeId = (int) $data['node_id'];
        $entity->nodeOwnerId = (string) $data['node_owner_id'];
        $entity->nodeListingId = (string) $data['node_listing_id'];
        $entity->nodeListingWP = (string) $data['node_listing_wp'];
        $entity->nodeListingName = (string) $data['node_listing_name'];
        $entity->nodeListingSize = (int) $data['node_listing_size'];
        $entity->nodeListingDifficulty = (int) $data['node_listing_difficulty'];
        $entity->nodeListingTerrain = (int) $data['node_listing_terrain'];
        $entity->nodeListingCoordinatesLon = (double) $data['node_listing_coordinates_lon'];
        $entity->nodeListingCoordinatesLat = (double) $data['node_listing_coordinates_lat'];
        $entity->nodeListingArchived = (bool) $data['node_listing_archived'];
        $entity->lastModified = new DateTime($data['last_modified']);
        $entity->importStatus = (int) $data['importstatus'];
        $entity->node = $this->nodesRepository->fetchOneBy(['id' => $entity->nodeId]);

        return $entity;
    }
}
