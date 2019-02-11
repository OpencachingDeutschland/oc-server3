<?php

namespace Oc\Country;

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CountryRepository
{
    /**
     * Database table name that this repository maintains.
     *
     * @var string
     */
    public const TABLE = 'countries';

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Fetches all countries.
     *
     * @throws RecordsNotFoundException Thrown when no records are found
     * @return CountryEntity[]
     */
    public function fetchAll(): array
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->execute();

        $result = $statement->fetchAll();

        if ($statement->rowCount() === 0) {
            throw new RecordsNotFoundException('No records found');
        }

        $countries = [];

        foreach ($result as $item) {
            $countries[] = $this->getEntityFromDatabaseArray($item);
        }

        return $countries;
    }

    /**
     * Creates a country in the database.
     *
     * @throws RecordAlreadyExistsException
     */
    public function create(CountryEntity $entity): CountryEntity
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->short = $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * Update a country in the database.
     *
     * @throws RecordNotPersistedException
     */
    public function update(CountryEntity $entity): CountryEntity
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

        $entity->short = $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * Removes a country from the database.
     *
     * @throws RecordNotPersistedException
     */
    public function remove(CountryEntity $entity): CountryEntity
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['short' => $entity->short]
        );

        $entity->short = null;

        return $entity;
    }

    /**
     * Maps the given entity to the database array.
     */
    public function getDatabaseArrayFromEntity(CountryEntity $entity): array
    {
        return [
            'short' => $entity->short,
            'name' => $entity->name,
            'de' => $entity->de,
            'en' => $entity->en,
            'trans_id' => $entity->translationId,
            'list_default_de' => $entity->listDefaultDe,
            'list_default_en' => $entity->listDefaultEn,
            'sort_de' => $entity->sortDe,
            'sort_en' => $entity->sortEn,
        ];
    }

    /**
     * Prepares database array from properties.
     */
    public function getEntityFromDatabaseArray(array $data): CountryEntity
    {
        $entity = new CountryEntity();
        $entity->short = $data['short'];
        $entity->name = $data['name'];
        $entity->de = $data['de'];
        $entity->en = $data['en'];
        $entity->translationId = (int) $data['trans_id'];
        $entity->listDefaultDe = (bool) $data['list_default_de'];
        $entity->listDefaultEn = (bool) $data['list_default_en'];
        $entity->sortDe = $data['sort_de'];
        $entity->sortEn = $data['sort_en'];

        return $entity;
    }
}
