<?php

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Oc\Entity\CachesEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CachesRepository
{
    /**
     * Database table name that this repository maintains.
     *
     * @var string
     */
    public const TABLE = 'caches';

    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var SecurityRolesRepository
     */
    private $securityRolesRepository; //??

    public function __construct(Connection $connection, SecurityRolesRepository $securityRolesRepository) //??
    {
        $this->connection = $connection;
        $this->securityRolesRepository = $securityRolesRepository;
    }

    /**
     * Fetches all caches.
     *
     * @return CachesEntity[]
     * @throws RecordsNotFoundException Thrown when no records are found
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

        return $this->getEntityArrayFromDatabaseArray($result);
    }

    /**
     * Fetches a cache by its id.
     *
     * @throws RecordNotFoundException Thrown when the request record is not found
     */
    public function fetchOneById(int $id): CachesEntity
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('cache_id = :id')
            ->setParameter(':id', $id)
            ->execute();

        $result = $statement->fetch();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException(sprintf(
                'Record with id #%s not found',
                $id
            ));
        }

        return $this->getEntityFromDatabaseArray($result);
    }

    /**
     * Prepares database array from properties.
     */
    public function getEntityFromDatabaseArray(array $data): CachesEntity
    {
        $entity = new CachesEntity();
        $entity->id = (int)$data['cache_id'];
        $entity->name = $data['name'];
        $entity->wp_gc = $data['wp_gc'];
        $entity->wp_oc = $data['wp_oc'];
        $entity->latitude = (double)$data['latitude'];
        $entity->longitude = (double)$data['longitude'];

        return $entity;
    }
}
