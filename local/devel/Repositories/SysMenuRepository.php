<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class SysMenuRepository
{
    const TABLE = 'sys_menu';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return SysMenuEntity[]
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
     * @return SysMenuEntity
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
     * @return SysMenuEntity[]
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
     * @param SysMenuEntity $entity
     * @return SysMenuEntity
     */
    public function create(SysMenuEntity $entity)
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
     * @param SysMenuEntity $entity
     * @return SysMenuEntity
     */
    public function update(SysMenuEntity $entity)
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
     * @param SysMenuEntity $entity
     * @return SysMenuEntity
     */
    public function remove(SysMenuEntity $entity)
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
     * @param SysMenuEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(SysMenuEntity $entity)
    {
        return [
        'id' => $entity->id,
        'id_string' => $entity->idString,
        'title' => $entity->title,
        'title_trans_id' => $entity->titleTransId,
        'menustring' => $entity->menustring,
        'menustring_trans_id' => $entity->menustringTransId,
        'access' => $entity->access,
        'href' => $entity->href,
        'visible' => $entity->visible,
        'parent' => $entity->parent,
        'position' => $entity->position,
        'color' => $entity->color,
        'sitemap' => $entity->sitemap,
        'only_if_parent' => $entity->onlyIfParent,
        ];
    }

    /**
     * @param array $data
     * @return SysMenuEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new SysMenuEntity();
        $entity->id = $data['id'];
        $entity->idString = $data['id_string'];
        $entity->title = $data['title'];
        $entity->titleTransId = $data['title_trans_id'];
        $entity->menustring = $data['menustring'];
        $entity->menustringTransId = $data['menustring_trans_id'];
        $entity->access = $data['access'];
        $entity->href = $data['href'];
        $entity->visible = $data['visible'];
        $entity->parent = $data['parent'];
        $entity->position = $data['position'];
        $entity->color = $data['color'];
        $entity->sitemap = $data['sitemap'];
        $entity->onlyIfParent = $data['only_if_parent'];

        return $entity;
    }
}
