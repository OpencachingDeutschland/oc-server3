<?php

namespace Oc\FieldNotes\Persistence;

use DateTime;
use Doctrine\DBAL\Connection;
use Oc\GeoCache\Persistence\GeoCache\GeoCacheRepository;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class FieldNoteRepository
{
    /**
     * Database table name that this repository maintains.
     *
     * @var string
     */
    const TABLE = 'field_note';

    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var GeoCacheRepository
     */
    private $geoCacheRepository;

    /**
     * @param Connection $connection
     * @param GeoCacheRepository $geoCacheRepository
     */
    public function __construct(Connection $connection, GeoCacheRepository $geoCacheRepository)
    {
        $this->connection = $connection;
        $this->geoCacheRepository = $geoCacheRepository;
    }

    /**
     * Fetches all countries.
     *
     * @return FieldNoteEntity[]
     *
     * @throws RecordsNotFoundException Thrown when no records are found
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

        $entities = [];

        foreach ($result as $item) {
            $entities[] = $this->getEntityFromDatabaseArray($item);
        }

        return $entities;
    }

    /**
     * Fetches all GeoCaches by given where clause.
     *
     * @param array $where
     *
     * @return FieldNoteEntity[]
     *
     * @throws RecordsNotFoundException Thrown when no records are found
     */
    public function fetchBy(array $where = [], array $order = [])
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE);

        foreach ($where as $column => $value) {
            $queryBuilder->andWhere($column . ' = ' .  $queryBuilder->createNamedParameter($value));
        }

        foreach ($order as $field => $direction) {
            $queryBuilder->addOrderBy($field, $direction);
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
     * Fetches a field note by given where clause.
     *
     * @param array $where
     *
     * @return null|FieldNoteEntity
     *
     * @throws RecordNotFoundException Thrown when no record is found
     */
    public function fetchOneBy(array $where = [])
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->setMaxResults(1);

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->andWhere($column . ' = ' .  $queryBuilder->createNamedParameter($value));
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
     * Fetch latest user field note.
     *
     * @param int $userId
     *
     * @return FieldNoteEntity
     *
     * @throws RecordNotFoundException
     */
    public function getLatestUserFieldNote($userId)
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('user_id = :userId')
            ->orderBy('date', 'DESC')
            ->setParameter('userId', $userId)
            ->setMaxResults(1);

        $statement = $queryBuilder->execute();

        $result = $statement->fetch();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException('Record with given where clause not found');
        }

        return $this->getEntityFromDatabaseArray($result);
    }

    /**
     * Creates a field note in the database.
     *
     * @param FieldNoteEntity $entity
     *
     * @return FieldNoteEntity
     *
     * @throws RecordAlreadyExistsException
     */
    public function create(FieldNoteEntity $entity)
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
     * Update a field note in the database.
     *
     * @param FieldNoteEntity $entity
     *
     * @return FieldNoteEntity
     *
     * @throws RecordNotPersistedException
     */
    public function update(FieldNoteEntity $entity)
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

        $entity->id = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * Removes a field note from the database.
     *
     * @param FieldNoteEntity $entity
     *
     * @return FieldNoteEntity
     *
     * @throws RecordNotPersistedException
     */
    public function remove(FieldNoteEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['id' => $entity->id]
        );

        $entity->id = null;

        return $entity;
    }

    /**
     * Maps the given entity to the database array.
     *
     * @param FieldNoteEntity $entity
     *
     * @return array
     */
    public function getDatabaseArrayFromEntity(FieldNoteEntity $entity)
    {
        return [
            'id' => $entity->id,
            'user_id' => $entity->userId,
            'geocache_id' => $entity->geocacheId,
            'type' => $entity->type,
            'date' => $entity->date->format(DateTime::ATOM),
            'text' => $entity->text
        ];
    }

    /**
     * Prepares database array from properties.
     *
     * @param array $data
     *
     * @return FieldNoteEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new FieldNoteEntity();
        $entity->id = $data['id'];
        $entity->userId = $data['user_id'];
        $entity->geocacheId = $data['geocache_id'];
        $entity->type = $data['type'];
        $entity->date = new DateTime($data['date']);
        $entity->text = $data['text'];
        $entity->geoCache = $this->geoCacheRepository->fetchOneBy([
            'cache_id' => $entity->geocacheId
        ]);

        return $entity;
    }
}
