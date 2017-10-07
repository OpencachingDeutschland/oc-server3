<?php

namespace Oc\Page;

use DateTime;
use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;

/**
 * Class PageRepository
 *
 * @package Oc\Page
 */
class PageRepository
{
    /**
     * Database table name that this repository maintains.
     *
     * @var string
     */
    const TABLE = 'page';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * PageRepository constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Fetches a page by slug.
     *
     * @param array $where
     *
     * @return null|PageEntity
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
     * Creates a page in the database.
     *
     * @param PageEntity $entity
     *
     * @return PageEntity
     *
     * @throws RecordAlreadyExistsException
     */
    public function create(PageEntity $entity)
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
     * @param PageEntity $entity
     *
     * @return PageEntity
     *
     * @throws RecordNotPersistedException
     */
    public function update(PageEntity $entity)
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
     * @param PageEntity $entity
     *
     * @return PageEntity
     *
     * @throws RecordNotPersistedException
     */
    public function remove(PageEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->delete(
            self::TABLE,
            $databaseArray,
            ['id' => $entity->id]
        );

        $entity->id = null;

        return $entity;
    }

    /**
     * Maps the given entity to the database array.
     *
     * @param PageEntity $entity
     *
     * @return array
     */
    public function getDatabaseArrayFromEntity(PageEntity $entity)
    {
        return [
            'id' => $entity->id,
            'slug' => $entity->slug,
            'meta_keywords' => $entity->metaKeywords,
            'meta_description' => $entity->metaDescription,
            'meta_social' => $entity->metaSocial,
            'updated_at' => $entity->updatedAt->format(DateTime::ATOM),
            'active' => $entity->active
        ];
    }

    /**
     * Prepares database array from properties.
     *
     * @param array $data
     *
     * @return PageEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new PageEntity();
        $entity->id = (int) $data['id'];
        $entity->slug = (string) $data['slug'];
        $entity->metaKeywords = (string) $data['meta_keywords'];
        $entity->metaDescription = (string) $data['meta_description'];
        $entity->metaSocial = (string) $data['meta_social'];
        $entity->updatedAt = new DateTime($data['updated_at']);
        $entity->active = (bool) $data['active'];

        return $entity;
    }
}
