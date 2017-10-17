<?php

namespace Oc\Page;

use DateTime;
use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

/**
 * Class BlockRepository
 *
 * @package Oc\Page
 */
class BlockRepository
{
    /**
     * Database table name that this repository maintains.
     *
     * @var string
     */
    const TABLE = 'page_block';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * BlockRepository constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Fetches all blocks of a page.
     *
     * @param array $where
     *
     * @return BlockEntity[]
     *
     * @throws RecordsNotFoundException Thrown when no records are found
     */
    public function fetchBy(array $where = [])
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE);

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->andWhere($column . ' = ' .  $queryBuilder->createNamedParameter($value));
            }
        }

        $statement = $queryBuilder->execute();

        $result = $statement->fetchAll();

        if ($statement->rowCount() === 0) {
            throw new RecordsNotFoundException('No records with given where clause found');
        }

        $blocks = [];

        foreach ($result as $item) {
            $blocks[] = $this->getEntityFromDatabaseArray($item);
        }

        return $blocks;
    }

    /**
     * Creates a block in the database.
     *
     * @param BlockEntity $entity
     *
     * @return BlockEntity
     *
     * @throws RecordAlreadyExistsException
     */
    public function create(BlockEntity $entity)
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
     * Update a page in the database.
     *
     * @param BlockEntity $entity
     *
     * @return BlockEntity
     *
     * @throws RecordNotPersistedException
     */
    public function update(BlockEntity $entity)
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
     * Removes a page from the database.
     *
     * @param BlockEntity $entity
     *
     * @return BlockEntity
     *
     * @throws RecordNotPersistedException
     */
    public function remove(BlockEntity $entity)
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
     * @param BlockEntity $entity
     *
     * @return array
     */
    public function getDatabaseArrayFromEntity(BlockEntity $entity)
    {
        return [
            'id' => $entity->id,
            'page_id' => $entity->pageId,
            'title' => $entity->title,
            'html' => $entity->html,
            'position' => (int) $entity->position,
            'updated_at' => $entity->updatedAt->format(DateTime::ATOM),
            'active' => $entity->active
        ];
    }

    /**
     * Prepares database array from properties.
     *
     * @param array $data
     *
     * @return BlockEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new BlockEntity();
        $entity->id = (int) $data['id'];
        $entity->pageId = (int) $data['page_id'];
        $entity->title = $data['title'];
        $entity->html = $data['html'];
        $entity->position = (int) $data['position'];
        $entity->updatedAt = new DateTime($data['updated_at']);
        $entity->active = (bool) $data['active'];

        return $entity;
    }
}
