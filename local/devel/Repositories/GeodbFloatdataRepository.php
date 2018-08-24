<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class GeodbFloatdataRepository
{
    const TABLE = 'geodb_floatdata';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return GeodbFloatdataEntity[]
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
     * @return GeodbFloatdataEntity
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
     * @return GeodbFloatdataEntity[]
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
     * @param GeodbFloatdataEntity $entity
     * @return GeodbFloatdataEntity
     */
    public function create(GeodbFloatdataEntity $entity)
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
     * @param GeodbFloatdataEntity $entity
     * @return GeodbFloatdataEntity
     */
    public function update(GeodbFloatdataEntity $entity)
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
     * @param GeodbFloatdataEntity $entity
     * @return GeodbFloatdataEntity
     */
    public function remove(GeodbFloatdataEntity $entity)
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
     * @param GeodbFloatdataEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(GeodbFloatdataEntity $entity)
    {
        return [
            'loc_id' => $entity->locId,
            'float_val' => $entity->floatVal,
            'float_type' => $entity->floatType,
            'float_subtype' => $entity->floatSubtype,
            'valid_since' => $entity->validSince,
            'date_type_since' => $entity->dateTypeSince,
            'valid_until' => $entity->validUntil,
            'date_type_until' => $entity->dateTypeUntil,
        ];
    }

    /**
     * @param array $data
     * @return GeodbFloatdataEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GeodbFloatdataEntity();
        $entity->locId = (int) $data['loc_id'];
        $entity->floatVal = $data['float_val'];
        $entity->floatType = (int) $data['float_type'];
        $entity->floatSubtype = (int) $data['float_subtype'];
        $entity->validSince = new DateTime($data['valid_since']);
        $entity->dateTypeSince = (int) $data['date_type_since'];
        $entity->validUntil = new DateTime($data['valid_until']);
        $entity->dateTypeUntil = (int) $data['date_type_until'];

        return $entity;
    }
}
