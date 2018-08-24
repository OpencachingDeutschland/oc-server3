<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class GeodbPolygonsRepository
{
    const TABLE = 'geodb_polygons';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return GeodbPolygonsEntity[]
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
     * @return GeodbPolygonsEntity
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
     * @return GeodbPolygonsEntity[]
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
     * @param GeodbPolygonsEntity $entity
     * @return GeodbPolygonsEntity
     */
    public function create(GeodbPolygonsEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
                    self::TABLE,
                    $databaseArray
                );

        $entity->polygonId = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param GeodbPolygonsEntity $entity
     * @return GeodbPolygonsEntity
     */
    public function update(GeodbPolygonsEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
                    self::TABLE,
                    $databaseArray,
                    ['polygon_id' => $entity->polygonId]
                );

        return $entity;
    }

    /**
     * @param GeodbPolygonsEntity $entity
     * @return GeodbPolygonsEntity
     */
    public function remove(GeodbPolygonsEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
                    self::TABLE,
                    ['polygon_id' => $entity->polygonId]
                );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param GeodbPolygonsEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(GeodbPolygonsEntity $entity)
    {
        return [
        'polygon_id' => $entity->polygonId,
        'seq_no' => $entity->seqNo,
        'lon' => $entity->lon,
        'lat' => $entity->lat,
        ];
    }

    /**
     * @param array $data
     * @return GeodbPolygonsEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GeodbPolygonsEntity();
        $entity->polygonId = (int) $data['polygon_id'];
        $entity->seqNo = (int) $data['seq_no'];
        $entity->lon = $data['lon'];
        $entity->lat = $data['lat'];

        return $entity;
    }
}
