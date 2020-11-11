<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class StatCachesRepository
{
    const TABLE = 'stat_caches';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return StatCachesEntity[]
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
     * @return StatCachesEntity
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
     * @return StatCachesEntity[]
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
     * @return StatCachesEntity
     */
    public function create(StatCachesEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->cacheId = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @return StatCachesEntity
     */
    public function update(StatCachesEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['cache_id' => $entity->cacheId]
        );

        return $entity;
    }

    /**
     * @return StatCachesEntity
     */
    public function remove(StatCachesEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['cache_id' => $entity->cacheId]
        );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @return []
     */
    public function getDatabaseArrayFromEntity(StatCachesEntity $entity)
    {
        return [
            'cache_id' => $entity->cacheId,
            'found' => $entity->found,
            'notfound' => $entity->notfound,
            'note' => $entity->note,
            'will_attend' => $entity->willAttend,
            'maintenance' => $entity->maintenance,
            'last_found' => $entity->lastFound,
            'watch' => $entity->watch,
            'ignore' => $entity->ignore,
            'toprating' => $entity->toprating,
            'picture' => $entity->picture,
        ];
    }

    /**
     * @return StatCachesEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new StatCachesEntity();
        $entity->cacheId = (int) $data['cache_id'];
        $entity->found = $data['found'];
        $entity->notfound = $data['notfound'];
        $entity->note = $data['note'];
        $entity->willAttend = $data['will_attend'];
        $entity->maintenance = $data['maintenance'];
        $entity->lastFound = new DateTime($data['last_found']);
        $entity->watch = $data['watch'];
        $entity->ignore = $data['ignore'];
        $entity->toprating = $data['toprating'];
        $entity->picture = $data['picture'];

        return $entity;
    }
}
