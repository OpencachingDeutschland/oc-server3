<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class UserDelegatesRepository
{
    const TABLE = 'user_delegates';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return UserDelegatesEntity[]
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
     * @return UserDelegatesEntity
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
     * @return UserDelegatesEntity[]
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
     * @param UserDelegatesEntity $entity
     * @return UserDelegatesEntity
     */
    public function create(UserDelegatesEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
                    self::TABLE,
                    $databaseArray
                );

        $entity->userId = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param UserDelegatesEntity $entity
     * @return UserDelegatesEntity
     */
    public function update(UserDelegatesEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
                    self::TABLE,
                    $databaseArray,
                    ['user_id' => $entity->userId]
                );

        return $entity;
    }

    /**
     * @param UserDelegatesEntity $entity
     * @return UserDelegatesEntity
     */
    public function remove(UserDelegatesEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
                    self::TABLE,
                    ['user_id' => $entity->userId]
                );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param UserDelegatesEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(UserDelegatesEntity $entity)
    {
        return [
        'user_id' => $entity->userId,
        'node' => $entity->node,
        'date_created' => $entity->dateCreated,
        ];
    }

    /**
     * @param array $data
     * @return UserDelegatesEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new UserDelegatesEntity();
        $entity->userId = (int) $data['user_id'];
        $entity->node = (int) $data['node'];
        $entity->dateCreated =  new DateTime($data['date_created']);

        return $entity;
    }
}
