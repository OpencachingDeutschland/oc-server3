<?php

namespace Oc\PageBlock;

use Oc\Util\DbalConnection;

class PageBlockRepository
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    public function __construct(DbalConnection $connection)
    {
        $this->connection = $connection->getConnection();
    }

    /**
     * @param int $id
     * @return PageBlockEntity
     */
    public function fetchOneById($id)
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('page_block')
            ->where('id = :id')
            ->setParameter(':id', $id)
            ->execute();

        $data = $statement->fetch(\PDO::FETCH_ASSOC);

        $entity = new PageBlockEntity();
        $entity->fromDatabaseArray($data);

        return $entity;
    }

    /**
     * @param PageBlockEntity $entity
     * @return PageBlockEntity
     */
    public function addPageBlock(PageBlockEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new \InvalidArgumentException('entity already exists');
        }

        $this->connection->insert(
            'page_block',
            $entity->toDatabaseArray()
        );

        $entity->id = (int) $this->connection->lastInsertId();

        return $entity;

    }

    /**
     * @param PageBlockEntity $entity
     * @return PageBlockEntity
     */
    public function updatePageBlock(PageBlockEntity $entity)
    {
        if ($entity->isNew()) {
            throw new \InvalidArgumentException('entity doesn\'t exists');
        }

        $this->connection->update(
            'page_block',
            $entity->toDatabaseArray(),
            ['id' => $entity->id]
        );

        $entity->id = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param PageBlockEntity $entity
     * @return PageBlockEntity
     */
    public function removePageBlock(PageBlockEntity $entity)
    {
        if ($entity->isNew()) {
            throw new \InvalidArgumentException('entity doesn\'t exists');
        }

        $this->connection->delete(
            'page_block',
            $entity->toDatabaseArray(),
            ['id' => $entity->id]
        );

        $entity->id = null;

        return $entity;
    }
}
