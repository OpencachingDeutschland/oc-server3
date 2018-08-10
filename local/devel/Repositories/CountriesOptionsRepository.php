<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CountriesOptionsRepository
{
    const TABLE = 'countries_options';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return CountriesOptionsEntity[]
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
     * @return CountriesOptionsEntity
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
     * @return CountriesOptionsEntity[]
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
     * @param CountriesOptionsEntity $entity
     * @return CountriesOptionsEntity
     */
    public function create(CountriesOptionsEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
                    self::TABLE,
                    $databaseArray
                );

        $entity->country = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param CountriesOptionsEntity $entity
     * @return CountriesOptionsEntity
     */
    public function update(CountriesOptionsEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
                    self::TABLE,
                    $databaseArray,
                    ['country' => $entity->country]
                );

        return $entity;
    }

    /**
     * @param CountriesOptionsEntity $entity
     * @return CountriesOptionsEntity
     */
    public function remove(CountriesOptionsEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
                    self::TABLE,
                    ['country' => $entity->country]
                );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param CountriesOptionsEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(CountriesOptionsEntity $entity)
    {
        return [
        'country' => $entity->country,
        'display' => $entity->display,
        'gmLat' => $entity->gmLat,
        'gmLon' => $entity->gmLon,
        'gmZoom' => $entity->gmZoom,
        'nodeId' => $entity->nodeId,
        ];
    }

    /**
     * @param array $data
     * @return CountriesOptionsEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new CountriesOptionsEntity();
        $entity->country = $data['country'];
        $entity->display = $data['display'];
        $entity->gmLat = $data['gmLat'];
        $entity->gmLon = $data['gmLon'];
        $entity->gmZoom = $data['gmZoom'];
        $entity->nodeId = $data['nodeId'];

        return $entity;
    }
}
