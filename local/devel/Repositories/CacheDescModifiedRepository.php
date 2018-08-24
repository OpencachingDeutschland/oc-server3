<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CacheDescModifiedRepository
{
    const TABLE = 'cache_desc_modified';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return GeoCacheDescModifiedEntity[]
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
     * @return GeoCacheDescModifiedEntity
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
     * @return GeoCacheDescModifiedEntity[]
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
     * @param GeoCacheDescModifiedEntity $entity
     * @return GeoCacheDescModifiedEntity
     */
    public function create(GeoCacheDescModifiedEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
                    self::TABLE,
                    $databaseArray
                );

        $entity->cacheId = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param GeoCacheDescModifiedEntity $entity
     * @return GeoCacheDescModifiedEntity
     */
    public function update(GeoCacheDescModifiedEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
                    self::TABLE,
                    $databaseArray,
                    ['cache_id' => $entity->cacheId]
                );

        return $entity;
    }

    /**
     * @param GeoCacheDescModifiedEntity $entity
     * @return GeoCacheDescModifiedEntity
     */
    public function remove(GeoCacheDescModifiedEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
                    self::TABLE,
                    ['cache_id' => $entity->cacheId]
                );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param GeoCacheDescModifiedEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(GeoCacheDescModifiedEntity $entity)
    {
        return [
        'cache_id' => $entity->cacheId,
        'language' => $entity->language,
        'date_modified' => $entity->dateModified,
        'date_created' => $entity->dateCreated,
        'desc' => $entity->desc,
        'desc_html' => $entity->descHtml,
        'desc_htmledit' => $entity->descHtmledit,
        'hint' => $entity->hint,
        'short_desc' => $entity->shortDesc,
        'restored_by' => $entity->restoredBy,
        ];
    }

    /**
     * @param array $data
     * @return GeoCacheDescModifiedEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GeoCacheDescModifiedEntity();
        $entity->cacheId = (int) $data['cache_id'];
        $entity->language = (string) $data['language'];
        $entity->dateModified =  new DateTime($data['date_modified']);
        $entity->dateCreated =  new DateTime($data['date_created']);
        $entity->desc = (string) $data['desc'];
        $entity->descHtml = (int) $data['desc_html'];
        $entity->descHtmledit = (int) $data['desc_htmledit'];
        $entity->hint = (string) $data['hint'];
        $entity->shortDesc = (string) $data['short_desc'];
        $entity->restoredBy = (int) $data['restored_by'];

        return $entity;
    }
}
