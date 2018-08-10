<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class GeodbCoordinatesRepository
{
    const TABLE = 'geodb_coordinates';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return GeodbCoordinatesEntity[]
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
     * @return GeodbCoordinatesEntity
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
     * @return GeodbCoordinatesEntity[]
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
     * @param GeodbCoordinatesEntity $entity
     * @return GeodbCoordinatesEntity
     */
    public function create(GeodbCoordinatesEntity $entity)
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
     * @param GeodbCoordinatesEntity $entity
     * @return GeodbCoordinatesEntity
     */
    public function update(GeodbCoordinatesEntity $entity)
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
     * @param GeodbCoordinatesEntity $entity
     * @return GeodbCoordinatesEntity
     */
    public function remove(GeodbCoordinatesEntity $entity)
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
     * @param GeodbCoordinatesEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(GeodbCoordinatesEntity $entity)
    {
        return [
        'loc_id' => $entity->locId,
        'lon' => $entity->lon,
        'lat' => $entity->lat,
        'coord_type' => $entity->coordType,
        'coord_subtype' => $entity->coordSubtype,
        'valid_since' => $entity->validSince,
        'date_type_since' => $entity->dateTypeSince,
        'valid_until' => $entity->validUntil,
        'date_type_until' => $entity->dateTypeUntil,
        ];
    }

    /**
     * @param array $data
     * @return GeodbCoordinatesEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GeodbCoordinatesEntity();
        $entity->locId = (int) $data['loc_id'];
        $entity->lon = $data['lon'];
        $entity->lat = $data['lat'];
        $entity->coordType = (int) $data['coord_type'];
        $entity->coordSubtype = (int) $data['coord_subtype'];
        $entity->validSince =  new DateTime($data['valid_since']);
        $entity->dateTypeSince = (int) $data['date_type_since'];
        $entity->validUntil =  new DateTime($data['valid_until']);
        $entity->dateTypeUntil = (int) $data['date_type_until'];

        return $entity;
    }
}
