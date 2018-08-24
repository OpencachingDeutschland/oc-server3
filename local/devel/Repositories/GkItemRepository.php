<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class GkItemRepository
{
    const TABLE = 'gk_item';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return GkItemEntity[]
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
     * @return GkItemEntity
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
     * @return GkItemEntity[]
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
     * @param GkItemEntity $entity
     * @return GkItemEntity
     */
    public function create(GkItemEntity $entity)
    {
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
     * @param GkItemEntity $entity
     * @return GkItemEntity
     */
    public function update(GkItemEntity $entity)
    {
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
     * @param GkItemEntity $entity
     * @return GkItemEntity
     */
    public function remove(GkItemEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
                    self::TABLE,
                    ['id' => $entity->id]
                );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param GkItemEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(GkItemEntity $entity)
    {
        return [
        'id' => $entity->id,
        'name' => $entity->name,
        'description' => $entity->description,
        'userid' => $entity->userid,
        'datecreated' => $entity->datecreated,
        'distancetravelled' => $entity->distancetravelled,
        'latitude' => $entity->latitude,
        'longitude' => $entity->longitude,
        'typeid' => $entity->typeid,
        'stateid' => $entity->stateid,
        ];
    }

    /**
     * @param array $data
     * @return GkItemEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GkItemEntity();
        $entity->id = (int) $data['id'];
        $entity->name = (string) $data['name'];
        $entity->description = (string) $data['description'];
        $entity->userid = (int) $data['userid'];
        $entity->datecreated =  new DateTime($data['datecreated']);
        $entity->distancetravelled = $data['distancetravelled'];
        $entity->latitude = $data['latitude'];
        $entity->longitude = $data['longitude'];
        $entity->typeid = (int) $data['typeid'];
        $entity->stateid = (int) $data['stateid'];

        return $entity;
    }
}
