<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class OkapiTokensRepository
{
    const TABLE = 'okapi_tokens';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return OkapiTokensEntity[]
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
     * @return OkapiTokensEntity
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
     * @return OkapiTokensEntity[]
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
     * @param OkapiTokensEntity $entity
     * @return OkapiTokensEntity
     */
    public function create(OkapiTokensEntity $entity)
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
     * @param OkapiTokensEntity $entity
     * @return OkapiTokensEntity
     */
    public function update(OkapiTokensEntity $entity)
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
     * @param OkapiTokensEntity $entity
     * @return OkapiTokensEntity
     */
    public function remove(OkapiTokensEntity $entity)
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
     * @param OkapiTokensEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(OkapiTokensEntity $entity)
    {
        return [
            'key' => $entity->key,
            'secret' => $entity->secret,
            'token_type' => $entity->tokenType,
            'timestamp' => $entity->timestamp,
            'user_id' => $entity->userId,
            'consumer_key' => $entity->consumerKey,
            'verifier' => $entity->verifier,
            'callback' => $entity->callback,
        ];
    }

    /**
     * @param array $data
     * @return OkapiTokensEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new OkapiTokensEntity();
        $entity->key = (string) $data['key'];
        $entity->secret = (string) $data['secret'];
        $entity->tokenType = $data['token_type'];
        $entity->timestamp = (int) $data['timestamp'];
        $entity->userId = (int) $data['user_id'];
        $entity->consumerKey = (string) $data['consumer_key'];
        $entity->verifier = (string) $data['verifier'];
        $entity->callback = (string) $data['callback'];

        return $entity;
    }
}
