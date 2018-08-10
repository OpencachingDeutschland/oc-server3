<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class SavedTextsRepository
{
    const TABLE = 'saved_texts';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return SavedTextsEntity[]
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
     * @return SavedTextsEntity
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
     * @return SavedTextsEntity[]
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
     * @param SavedTextsEntity $entity
     * @return SavedTextsEntity
     */
    public function create(SavedTextsEntity $entity)
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
     * @param SavedTextsEntity $entity
     * @return SavedTextsEntity
     */
    public function update(SavedTextsEntity $entity)
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
     * @param SavedTextsEntity $entity
     * @return SavedTextsEntity
     */
    public function remove(SavedTextsEntity $entity)
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
     * @param SavedTextsEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(SavedTextsEntity $entity)
    {
        return [
        'id' => $entity->id,
        'date_created' => $entity->dateCreated,
        'object_type' => $entity->objectType,
        'object_id' => $entity->objectId,
        'subtype' => $entity->subtype,
        'text' => $entity->text,
        ];
    }

    /**
     * @param array $data
     * @return SavedTextsEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new SavedTextsEntity();
        $entity->id = $data['id'];
        $entity->dateCreated = $data['date_created'];
        $entity->objectType = $data['object_type'];
        $entity->objectId = $data['object_id'];
        $entity->subtype = $data['subtype'];
        $entity->text = $data['text'];

        return $entity;
    }
}
