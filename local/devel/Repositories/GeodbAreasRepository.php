<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class GeodbAreasRepository
{
    const TABLE = 'geodb_areas';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return GeodbAreasEntity[]
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
     * @return GeodbAreasEntity
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
     * @return GeodbAreasEntity[]
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
     * @param GeodbAreasEntity $entity
     * @return GeodbAreasEntity
     */
    public function create(GeodbAreasEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
                    self::TABLE,
                    $databaseArray
                );

        $entity->locId = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param GeodbAreasEntity $entity
     * @return GeodbAreasEntity
     */
    public function update(GeodbAreasEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
                    self::TABLE,
                    $databaseArray,
                    ['loc_id' => $entity->locId]
                );

        return $entity;
    }

    /**
     * @param GeodbAreasEntity $entity
     * @return GeodbAreasEntity
     */
    public function remove(GeodbAreasEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
                    self::TABLE,
                    ['loc_id' => $entity->locId]
                );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param GeodbAreasEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(GeodbAreasEntity $entity)
    {
        return [
        'loc_id' => $entity->locId,
        'area_id' => $entity->areaId,
        'polygon_id' => $entity->polygonId,
        'pol_seq_no' => $entity->polSeqNo,
        'exclude_area' => $entity->excludeArea,
        'area_type' => $entity->areaType,
        'area_subtype' => $entity->areaSubtype,
        'coord_type' => $entity->coordType,
        'coord_subtype' => $entity->coordSubtype,
        'resolution' => $entity->resolution,
        'valid_since' => $entity->validSince,
        'date_type_since' => $entity->dateTypeSince,
        'valid_until' => $entity->validUntil,
        'date_type_until' => $entity->dateTypeUntil,
        ];
    }

    /**
     * @param array $data
     * @return GeodbAreasEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GeodbAreasEntity();
        $entity->locId = $data['loc_id'];
        $entity->areaId = $data['area_id'];
        $entity->polygonId = $data['polygon_id'];
        $entity->polSeqNo = $data['pol_seq_no'];
        $entity->excludeArea = $data['exclude_area'];
        $entity->areaType = $data['area_type'];
        $entity->areaSubtype = $data['area_subtype'];
        $entity->coordType = $data['coord_type'];
        $entity->coordSubtype = $data['coord_subtype'];
        $entity->resolution = $data['resolution'];
        $entity->validSince = $data['valid_since'];
        $entity->dateTypeSince = $data['date_type_since'];
        $entity->validUntil = $data['valid_until'];
        $entity->dateTypeUntil = $data['date_type_until'];

        return $entity;
    }
}
