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
     * @param array $where
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
     * @param array $where
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
     * @param GeoCacheAttribEntity $entity
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
     * @param GeoCacheAttribEntity $entity
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
     * @param GeoCacheAttribEntity $entity
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
     * @param GeoCacheAttribEntity $entity
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
     * @param array $data
     * @return GeoCacheAttribEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GeoCacheAttribEntity();
        $entity->id = $data['id'];
        $entity->name = $data['name'];
        $entity->icon = $data['icon'];
        $entity->transId = $data['trans_id'];
        $entity->groupId = $data['group_id'];
        $entity->selectable = $data['selectable'];
        $entity->category = $data['category'];
        $entity->searchDefault = $data['search_default'];
        $entity->default = $data['default'];
        $entity->iconLarge = $data['icon_large'];
        $entity->iconNo = $data['icon_no'];
        $entity->iconUndef = $data['icon_undef'];
        $entity->htmlDesc = $data['html_desc'];
        $entity->htmlDescTransId = $data['html_desc_trans_id'];
        $entity->hidden = $data['hidden'];
        $entity->gcId = $data['gc_id'];
        $entity->gcInc = $data['gc_inc'];
        $entity->gcName = $data['gc_name'];

        return $entity;
    }
}
