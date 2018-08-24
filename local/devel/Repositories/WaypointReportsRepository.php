<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class WaypointReportsRepository
{
    const TABLE = 'waypoint_reports';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return WaypointReportsEntity[]
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

        $records = [];

        foreach ($result as $item) {
            $records[] = $this->getEntityFromDatabaseArray($item);
        }

        return $records;
    }

    /**
     * @param array $where
     * @return WaypointReportsEntity
     */
    public function fetchOneBy(array $where = [])
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

        $statement = $queryBuilder->execute();

        $result = $statement->fetch();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException('Record with given where clause not found');
        }

        return $this->getEntityFromDatabaseArray($result);
    }

    /**
     * @param array $where
     * @return WaypointReportsEntity[]
     */
    public function fetchBy(array $where = [])
    {
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
     * @param WaypointReportsEntity $entity
     * @return WaypointReportsEntity
     */
    public function create(WaypointReportsEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->reportId = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param WaypointReportsEntity $entity
     * @return WaypointReportsEntity
     */
    public function update(WaypointReportsEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['report_id' => $entity->reportId]
        );

        return $entity;
    }

    /**
     * @param WaypointReportsEntity $entity
     * @return WaypointReportsEntity
     */
    public function remove(WaypointReportsEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['report_id' => $entity->reportId]
        );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param WaypointReportsEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(WaypointReportsEntity $entity)
    {
        return [
            'report_id' => $entity->reportId,
            'date_reported' => $entity->dateReported,
            'wp_oc' => $entity->wpOc,
            'wp_external' => $entity->wpExternal,
            'source' => $entity->source,
            'gcwp_processed' => $entity->gcwpProcessed,
        ];
    }

    /**
     * @param array $data
     * @return WaypointReportsEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new WaypointReportsEntity();
        $entity->reportId = (int) $data['report_id'];
        $entity->dateReported = new DateTime($data['date_reported']);
        $entity->wpOc = (string) $data['wp_oc'];
        $entity->wpExternal = (string) $data['wp_external'];
        $entity->source = (string) $data['source'];
        $entity->gcwpProcessed = (int) $data['gcwp_processed'];

        return $entity;
    }
}
