<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class XmlsessionRepository
{
    const TABLE = 'xmlsession';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return XmlsessionEntity[]
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
     * @return XmlsessionEntity
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
     * @return XmlsessionEntity[]
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
     * @param XmlsessionEntity $entity
     * @return XmlsessionEntity
     */
    public function create(XmlsessionEntity $entity)
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
     * @param XmlsessionEntity $entity
     * @return XmlsessionEntity
     */
    public function update(XmlsessionEntity $entity)
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
     * @param XmlsessionEntity $entity
     * @return XmlsessionEntity
     */
    public function remove(XmlsessionEntity $entity)
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
     * @param XmlsessionEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(XmlsessionEntity $entity)
    {
        return [
        'id' => $entity->id,
        'date_created' => $entity->dateCreated,
        'last_use' => $entity->lastUse,
        'users' => $entity->users,
        'caches' => $entity->caches,
        'cachedescs' => $entity->cachedescs,
        'cachelogs' => $entity->cachelogs,
        'pictures' => $entity->pictures,
        'removedobjects' => $entity->removedobjects,
        'modified_since' => $entity->modifiedSince,
        'cleaned' => $entity->cleaned,
        'agent' => $entity->agent,
        ];
    }

    /**
     * @param array $data
     * @return XmlsessionEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new XmlsessionEntity();
        $entity->id = $data['id'];
        $entity->dateCreated = $data['date_created'];
        $entity->lastUse = $data['last_use'];
        $entity->users = $data['users'];
        $entity->caches = $data['caches'];
        $entity->cachedescs = $data['cachedescs'];
        $entity->cachelogs = $data['cachelogs'];
        $entity->pictures = $data['pictures'];
        $entity->removedobjects = $data['removedobjects'];
        $entity->modifiedSince = $data['modified_since'];
        $entity->cleaned = $data['cleaned'];
        $entity->agent = $data['agent'];

        return $entity;
    }
}
