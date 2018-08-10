<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class GeodbHierarchiesRepository
{
    const TABLE = 'geodb_hierarchies';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return GeodbHierarchiesEntity[]
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
     * @return GeodbHierarchiesEntity
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
     * @return GeodbHierarchiesEntity[]
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
     * @param GeodbHierarchiesEntity $entity
     * @return GeodbHierarchiesEntity
     */
    public function create(GeodbHierarchiesEntity $entity)
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
     * @param GeodbHierarchiesEntity $entity
     * @return GeodbHierarchiesEntity
     */
    public function update(GeodbHierarchiesEntity $entity)
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
     * @param GeodbHierarchiesEntity $entity
     * @return GeodbHierarchiesEntity
     */
    public function remove(GeodbHierarchiesEntity $entity)
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
     * @param GeodbHierarchiesEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(GeodbHierarchiesEntity $entity)
    {
        return [
        'loc_id' => $entity->locId,
        'level' => $entity->level,
        'id_lvl1' => $entity->idLvl1,
        'id_lvl2' => $entity->idLvl2,
        'id_lvl3' => $entity->idLvl3,
        'id_lvl4' => $entity->idLvl4,
        'id_lvl5' => $entity->idLvl5,
        'id_lvl6' => $entity->idLvl6,
        'id_lvl7' => $entity->idLvl7,
        'id_lvl8' => $entity->idLvl8,
        'id_lvl9' => $entity->idLvl9,
        'valid_since' => $entity->validSince,
        'date_type_since' => $entity->dateTypeSince,
        'valid_until' => $entity->validUntil,
        'date_type_until' => $entity->dateTypeUntil,
        ];
    }

    /**
     * @param array $data
     * @return GeodbHierarchiesEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GeodbHierarchiesEntity();
        $entity->locId = $data['loc_id'];
        $entity->level = $data['level'];
        $entity->idLvl1 = $data['id_lvl1'];
        $entity->idLvl2 = $data['id_lvl2'];
        $entity->idLvl3 = $data['id_lvl3'];
        $entity->idLvl4 = $data['id_lvl4'];
        $entity->idLvl5 = $data['id_lvl5'];
        $entity->idLvl6 = $data['id_lvl6'];
        $entity->idLvl7 = $data['id_lvl7'];
        $entity->idLvl8 = $data['id_lvl8'];
        $entity->idLvl9 = $data['id_lvl9'];
        $entity->validSince = $data['valid_since'];
        $entity->dateTypeSince = $data['date_type_since'];
        $entity->validUntil = $data['valid_until'];
        $entity->dateTypeUntil = $data['date_type_until'];

        return $entity;
    }
}
