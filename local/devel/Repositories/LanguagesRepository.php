<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class LanguagesRepository
{
    const TABLE = 'languages';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return LanguagesEntity[]
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
     * @return LanguagesEntity
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
     * @return LanguagesEntity[]
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
     * @param LanguagesEntity $entity
     * @return LanguagesEntity
     */
    public function create(LanguagesEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
                    self::TABLE,
                    $databaseArray
                );

        $entity->short = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param LanguagesEntity $entity
     * @return LanguagesEntity
     */
    public function update(LanguagesEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
                    self::TABLE,
                    $databaseArray,
                    ['short' => $entity->short]
                );

        return $entity;
    }

    /**
     * @param LanguagesEntity $entity
     * @return LanguagesEntity
     */
    public function remove(LanguagesEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
                    self::TABLE,
                    ['short' => $entity->short]
                );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param LanguagesEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(LanguagesEntity $entity)
    {
        return [
        'short' => $entity->short,
        'name' => $entity->name,
        'trans_id' => $entity->transId,
        'native_name' => $entity->nativeName,
        'de' => $entity->de,
        'en' => $entity->en,
        'list_default_de' => $entity->listDefaultDe,
        'list_default_en' => $entity->listDefaultEn,
        'is_translated' => $entity->isTranslated,
        ];
    }

    /**
     * @param array $data
     * @return LanguagesEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new LanguagesEntity();
        $entity->short = $data['short'];
        $entity->name = $data['name'];
        $entity->transId = $data['trans_id'];
        $entity->nativeName = $data['native_name'];
        $entity->de = $data['de'];
        $entity->en = $data['en'];
        $entity->listDefaultDe = $data['list_default_de'];
        $entity->listDefaultEn = $data['list_default_en'];
        $entity->isTranslated = $data['is_translated'];

        return $entity;
    }
}
