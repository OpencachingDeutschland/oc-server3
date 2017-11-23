<?php

namespace Oc\Language;

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

/**
 * Class LanguageRepository
 */
class LanguageRepository
{
    /**
     * Database table name that this repository maintains.
     *
     * @var string
     */
    const TABLE = 'languages';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * LanguageRepository constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Fetches all languages.
     *
     * @throws RecordsNotFoundException Thrown when no records are found
     * @return LanguageEntity[]
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

        return $this->getEntityArrayFromDatabaseArray($result);
    }

    /**
     * Fetches all translated languages.
     *
     * @throws RecordsNotFoundException Thrown when no records are found
     * @return LanguageEntity[]
     */
    public function fetchAllTranslated()
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('is_translated = 1')
            ->execute();

        $result = $statement->fetchAll();

        if ($statement->rowCount() === 0) {
            throw new RecordsNotFoundException('No records with given where clause found');
        }

        return $this->getEntityArrayFromDatabaseArray($result);
    }

    /**
     * Creates a language in the database.
     *
     * @param LanguageEntity $entity
     *
     * @throws RecordAlreadyExistsException
     * @return LanguageEntity
     */
    public function create(LanguageEntity $entity)
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
     * Update a language in the database.
     *
     * @param LanguageEntity $entity
     *
     * @throws RecordNotPersistedException
     * @return LanguageEntity
     */
    public function update(LanguageEntity $entity)
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
     * Removes a language from the database.
     *
     * @param LanguageEntity $entity
     *
     * @throws RecordNotPersistedException
     * @return LanguageEntity
     */
    public function remove(LanguageEntity $entity)
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
     * Converts database array to entity array.
     *
     * @param array $result
     *
     * @return LanguageEntity[]
     */
    private function getEntityArrayFromDatabaseArray(array $result)
    {
        $languages = [];

        foreach ($result as $item) {
            $languages[] = $this->getEntityFromDatabaseArray($item);
        }

        return $languages;
    }

    /**
     * Maps the given entity to the database array.
     *
     * @param LanguageEntity $entity
     *
     * @return array
     */
    public function getDatabaseArrayFromEntity(LanguageEntity $entity)
    {
        return [
            'short' => $entity->short,
            'name' => $entity->name,
            'native_name' => $entity->nativeName,
            'de' => $entity->de,
            'en' => $entity->en,
            'trans_id' => $entity->translationId,
            'list_default_de' => $entity->listDefaultDe,
            'list_default_en' => $entity->listDefaultEn,
            'is_translated' => $entity->isTranslated,
        ];
    }

    /**
     * Prepares database array from properties.
     *
     * @param array $data
     *
     * @return LanguageEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new LanguageEntity();
        $entity->short = strtolower($data['short']);
        $entity->name = $data['name'];
        $entity->nativeName = $data['native_name'];
        $entity->de = $data['de'];
        $entity->en = $data['en'];
        $entity->translationId = (int) $data['trans_id'];
        $entity->listDefaultDe = (bool) $data['list_default_de'];
        $entity->listDefaultEn = (bool) $data['list_default_en'];
        $entity->isTranslated = (bool) $data['is_translated'];

        return $entity;
    }
}
