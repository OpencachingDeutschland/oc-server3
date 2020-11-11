<?php

namespace Oc\Page\Persistence;

use DateTime;
use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;

class PageRepository
{
    /**
     * Database table name that this repository maintains.
     *
     * @var string
     */
    public const TABLE = 'page';

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Fetches a page by slug.
     *
     * @throws RecordNotFoundException Thrown when no record is found
     */
    public function fetchOneBy(array $where = []): ?PageEntity
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
     * Creates a page in the database.
     *
     * @throws RecordAlreadyExistsException
     */
    public function create(PageEntity $entity): PageEntity
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
     * @throws RecordNotPersistedException
     */
    public function update(PageEntity $entity): PageEntity
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
     * @throws RecordNotPersistedException
     */
    public function remove(PageEntity $entity): PageEntity
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
     */
    public function getDatabaseArrayFromEntity(PageEntity $entity): array
    {
        return [
            'id' => $entity->id,
            'slug' => $entity->slug,
            'meta_keywords' => $entity->metaKeywords,
            'meta_description' => $entity->metaDescription,
            'meta_social' => $entity->metaSocial,
            'updated_at' => $entity->updatedAt->format(DateTime::ATOM),
            'active' => $entity->active,
        ];
    }

    /**
     * Prepares database array from properties.
     */
    public function getEntityFromDatabaseArray(array $data): PageEntity
    {
        $entity = new PageEntity();
        $entity->id = (int) $data['id'];
        $entity->slug = $data['slug'];
        $entity->metaKeywords = $data['meta_keywords'];
        $entity->metaDescription = $data['meta_description'];
        $entity->metaSocial = $data['meta_social'];
        $entity->updatedAt = new DateTime($data['updated_at']);
        $entity->active = (bool) $data['active'];

        return $entity;
    }
}
