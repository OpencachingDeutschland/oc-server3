<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class PicturesRepository
{
    const TABLE = 'pictures';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return PicturesEntity[]
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
     * @return PicturesEntity
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
     * @return PicturesEntity[]
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
     * @param PicturesEntity $entity
     * @return PicturesEntity
     */
    public function create(PicturesEntity $entity)
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
     * @param PicturesEntity $entity
     * @return PicturesEntity
     */
    public function update(PicturesEntity $entity)
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
     * @param PicturesEntity $entity
     * @return PicturesEntity
     */
    public function remove(PicturesEntity $entity)
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
     * @param PicturesEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(PicturesEntity $entity)
    {
        return [
            'id' => $entity->id,
            'uuid' => $entity->uuid,
            'node' => $entity->node,
            'date_created' => $entity->dateCreated,
            'last_modified' => $entity->lastModified,
            'url' => $entity->url,
            'title' => $entity->title,
            'last_url_check' => $entity->lastUrlCheck,
            'object_id' => $entity->objectId,
            'object_type' => $entity->objectType,
            'thumb_url' => $entity->thumbUrl,
            'thumb_last_generated' => $entity->thumbLastGenerated,
            'spoiler' => $entity->spoiler,
            'local' => $entity->local,
            'unknown_format' => $entity->unknownFormat,
            'display' => $entity->display,
            'mappreview' => $entity->mappreview,
            'seq' => $entity->seq,
        ];
    }

    /**
     * @param array $data
     * @return PicturesEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new PicturesEntity();
        $entity->id = (int) $data['id'];
        $entity->uuid = (string) $data['uuid'];
        $entity->node = (int) $data['node'];
        $entity->dateCreated = new DateTime($data['date_created']);
        $entity->lastModified = new DateTime($data['last_modified']);
        $entity->url = (string) $data['url'];
        $entity->title = (string) $data['title'];
        $entity->lastUrlCheck = new DateTime($data['last_url_check']);
        $entity->objectId = (int) $data['object_id'];
        $entity->objectType = (int) $data['object_type'];
        $entity->thumbUrl = (string) $data['thumb_url'];
        $entity->thumbLastGenerated = new DateTime($data['thumb_last_generated']);
        $entity->spoiler = (int) $data['spoiler'];
        $entity->local = (int) $data['local'];
        $entity->unknownFormat = (int) $data['unknown_format'];
        $entity->display = (int) $data['display'];
        $entity->mappreview = (int) $data['mappreview'];
        $entity->seq = $data['seq'];

        return $entity;
    }
}
