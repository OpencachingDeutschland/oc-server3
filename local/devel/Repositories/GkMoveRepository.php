<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class GkMoveRepository
{
    const TABLE = 'gk_move';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return GkMoveEntity[]
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
     * @return GkMoveEntity
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
     * @return GkMoveEntity[]
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
     * @param GkMoveEntity $entity
     * @return GkMoveEntity
     */
    public function create(GkMoveEntity $entity)
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
     * @param GkMoveEntity $entity
     * @return GkMoveEntity
     */
    public function update(GkMoveEntity $entity)
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
     * @param GkMoveEntity $entity
     * @return GkMoveEntity
     */
    public function remove(GkMoveEntity $entity)
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
     * @param GkMoveEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(GkMoveEntity $entity)
    {
        return [
            'id' => $entity->id,
            'itemid' => $entity->itemid,
            'latitude' => $entity->latitude,
            'longitude' => $entity->longitude,
            'datemoved' => $entity->datemoved,
            'datelogged' => $entity->datelogged,
            'userid' => $entity->userid,
            'comment' => $entity->comment,
            'logtypeid' => $entity->logtypeid,
        ];
    }

    /**
     * @param array $data
     * @return GkMoveEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GkMoveEntity();
        $entity->id = (int) $data['id'];
        $entity->itemid = (int) $data['itemid'];
        $entity->latitude = $data['latitude'];
        $entity->longitude = $data['longitude'];
        $entity->datemoved = new DateTime($data['datemoved']);
        $entity->datelogged = new DateTime($data['datelogged']);
        $entity->userid = (int) $data['userid'];
        $entity->comment = (string) $data['comment'];
        $entity->logtypeid = (int) $data['logtypeid'];

        return $entity;
    }
}
