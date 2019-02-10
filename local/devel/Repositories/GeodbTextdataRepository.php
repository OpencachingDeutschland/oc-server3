<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class GeodbTextdataRepository
{
    const TABLE = 'geodb_textdata';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return GeodbTextdataEntity[]
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
     * @return GeodbTextdataEntity
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
     * @return GeodbTextdataEntity[]
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
     * @return GeodbTextdataEntity
     */
    public function create(GeodbTextdataEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->locId = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @return GeodbTextdataEntity
     */
    public function update(GeodbTextdataEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['loc_id' => $entity->locId]
        );

        return $entity;
    }

    /**
     * @return GeodbTextdataEntity
     */
    public function remove(GeodbTextdataEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['loc_id' => $entity->locId]
        );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @return []
     */
    public function getDatabaseArrayFromEntity(GeodbTextdataEntity $entity)
    {
        return [
            'loc_id' => $entity->locId,
            'text_val' => $entity->textVal,
            'text_type' => $entity->textType,
            'text_locale' => $entity->textLocale,
            'is_native_lang' => $entity->isNativeLang,
            'is_default_name' => $entity->isDefaultName,
            'valid_since' => $entity->validSince,
            'date_type_since' => $entity->dateTypeSince,
            'valid_until' => $entity->validUntil,
            'date_type_until' => $entity->dateTypeUntil,
        ];
    }

    /**
     * @return GeodbTextdataEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GeodbTextdataEntity();
        $entity->locId = (int) $data['loc_id'];
        $entity->textVal = (string) $data['text_val'];
        $entity->textType = (int) $data['text_type'];
        $entity->textLocale = (string) $data['text_locale'];
        $entity->isNativeLang = $data['is_native_lang'];
        $entity->isDefaultName = $data['is_default_name'];
        $entity->validSince = new DateTime($data['valid_since']);
        $entity->dateTypeSince = (int) $data['date_type_since'];
        $entity->validUntil = new DateTime($data['valid_until']);
        $entity->dateTypeUntil = (int) $data['date_type_until'];

        return $entity;
    }
}
