<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class OkapiConsumersRepository
{
    const TABLE = 'okapi_consumers';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return OkapiConsumersEntity[]
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
     * @return OkapiConsumersEntity
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
     * @return OkapiConsumersEntity[]
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
     * @param OkapiConsumersEntity $entity
     * @return OkapiConsumersEntity
     */
    public function create(OkapiConsumersEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
                    self::TABLE,
                    $databaseArray
                );

        $entity->key = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param OkapiConsumersEntity $entity
     * @return OkapiConsumersEntity
     */
    public function update(OkapiConsumersEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
                    self::TABLE,
                    $databaseArray,
                    ['key' => $entity->key]
                );

        return $entity;
    }

    /**
     * @param OkapiConsumersEntity $entity
     * @return OkapiConsumersEntity
     */
    public function remove(OkapiConsumersEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
                    self::TABLE,
                    ['key' => $entity->key]
                );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param OkapiConsumersEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(OkapiConsumersEntity $entity)
    {
        return [
        'key' => $entity->key,
        'name' => $entity->name,
        'secret' => $entity->secret,
        'url' => $entity->url,
        'email' => $entity->email,
        'date_created' => $entity->dateCreated,
        'bflags' => $entity->bflags,
        ];
    }

    /**
     * @param array $data
     * @return OkapiConsumersEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new OkapiConsumersEntity();
        $entity->key = (string) $data['key'];
        $entity->name = (string) $data['name'];
        $entity->secret = (string) $data['secret'];
        $entity->url = (string) $data['url'];
        $entity->email = (string) $data['email'];
        $entity->dateCreated =  new DateTime($data['date_created']);
        $entity->bflags = (int) $data['bflags'];

        return $entity;
    }
}
