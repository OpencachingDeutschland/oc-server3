<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class OkapiTileCachesRepository
{
    const TABLE = 'okapi_tile_caches';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return OkapiTileCachesEntity[]
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
     * @return OkapiTileCachesEntity
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
     * @return OkapiTileCachesEntity[]
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
     * @param OkapiTileCachesEntity $entity
     * @return OkapiTileCachesEntity
     */
    public function create(OkapiTileCachesEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->z = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param OkapiTileCachesEntity $entity
     * @return OkapiTileCachesEntity
     */
    public function update(OkapiTileCachesEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['z' => $entity->z]
        );

        return $entity;
    }

    /**
     * @param OkapiTileCachesEntity $entity
     * @return OkapiTileCachesEntity
     */
    public function remove(OkapiTileCachesEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['z' => $entity->z]
        );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param OkapiTileCachesEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(OkapiTileCachesEntity $entity)
    {
        return [
            'z' => $entity->z,
            'x' => $entity->x,
            'y' => $entity->y,
            'cache_id' => $entity->cacheId,
            'z21x' => $entity->z21x,
            'z21y' => $entity->z21y,
            'status' => $entity->status,
            'type' => $entity->type,
            'rating' => $entity->rating,
            'flags' => $entity->flags,
            'name_crc' => $entity->nameCrc,
        ];
    }

    /**
     * @param array $data
     * @return OkapiTileCachesEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new OkapiTileCachesEntity();
        $entity->z = (int) $data['z'];
        $entity->x = (int) $data['x'];
        $entity->y = (int) $data['y'];
        $entity->cacheId = (int) $data['cache_id'];
        $entity->z21x = (int) $data['z21x'];
        $entity->z21y = (int) $data['z21y'];
        $entity->status = (int) $data['status'];
        $entity->type = (int) $data['type'];
        $entity->rating = (int) $data['rating'];
        $entity->flags = (int) $data['flags'];
        $entity->nameCrc = (int) $data['name_crc'];

        return $entity;
    }
}
