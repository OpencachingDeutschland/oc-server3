<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CacheAttribRepository
{
    const TABLE = 'cache_attrib';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return GeoCacheAttribEntity[]
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
     * @return GeoCacheAttribEntity
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
     * @return GeoCacheAttribEntity[]
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
     * @return GeoCacheAttribEntity
     */
    public function create(GeoCacheAttribEntity $entity)
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
     * @return GeoCacheAttribEntity
     */
    public function update(GeoCacheAttribEntity $entity)
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
     * @return GeoCacheAttribEntity
     */
    public function remove(GeoCacheAttribEntity $entity)
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
     * @return []
     */
    public function getDatabaseArrayFromEntity(GeoCacheAttribEntity $entity)
    {
        return [
            'id' => $entity->id,
            'name' => $entity->name,
            'icon' => $entity->icon,
            'trans_id' => $entity->transId,
            'group_id' => $entity->groupId,
            'selectable' => $entity->selectable,
            'category' => $entity->category,
            'search_default' => $entity->searchDefault,
            'default' => $entity->default,
            'icon_large' => $entity->iconLarge,
            'icon_no' => $entity->iconNo,
            'icon_undef' => $entity->iconUndef,
            'html_desc' => $entity->htmlDesc,
            'html_desc_trans_id' => $entity->htmlDescTransId,
            'hidden' => $entity->hidden,
            'gc_id' => $entity->gcId,
            'gc_inc' => $entity->gcInc,
            'gc_name' => $entity->gcName,
        ];
    }

    /**
     * @return GeoCacheAttribEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GeoCacheAttribEntity();
        $entity->id = (int) $data['id'];
        $entity->name = (string) $data['name'];
        $entity->icon = (string) $data['icon'];
        $entity->transId = (int) $data['trans_id'];
        $entity->groupId = (int) $data['group_id'];
        $entity->selectable = (int) $data['selectable'];
        $entity->category = (int) $data['category'];
        $entity->searchDefault = (int) $data['search_default'];
        $entity->default = (int) $data['default'];
        $entity->iconLarge = (string) $data['icon_large'];
        $entity->iconNo = (string) $data['icon_no'];
        $entity->iconUndef = (string) $data['icon_undef'];
        $entity->htmlDesc = (string) $data['html_desc'];
        $entity->htmlDescTransId = (int) $data['html_desc_trans_id'];
        $entity->hidden = (int) $data['hidden'];
        $entity->gcId = (int) $data['gc_id'];
        $entity->gcInc = (int) $data['gc_inc'];
        $entity->gcName = (string) $data['gc_name'];

        return $entity;
    }
}
