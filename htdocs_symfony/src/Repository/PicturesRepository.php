<?php

declare(strict_types=1);

namespace Oc\Repository;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class PicturesRepository
{
    private const TABLE = 'pictures';

    private Connection $connection;

    private LogTypesRepository $logTypesRepository;

    private UserRepository $userRepository;

    public function __construct(
            Connection $connection,
            LogTypesRepository $logTypesRepository,
            UserRepository $userRepository
    ) {
        $this->connection = $connection;
        $this->logTypesRepository = $logTypesRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws RecordsNotFoundException
     * @throws Exception
     * @throws \Exception
     */
    public function fetchAll(): array
    {
        $statement = $this->connection->createQueryBuilder()
                ->select('*')
                ->from(self::TABLE)
                ->executeQuery();

        $result = $statement->fetchAllAssociative();

        if ($statement->rowCount() === 0) {
            throw new RecordsNotFoundException('No records found');
        }

        $records = [];

        foreach ($result as $item) {
            $records[] = $item;
        }

        return $records;
    }

    /**
     * @throws RecordNotFoundException
     * @throws Exception
     * @throws \Exception
     */
    public function fetchOneBy(array $where = []): PicturesEntity
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

        $statement = $queryBuilder->executeQuery();

        $result = $statement->fetchAssociative();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException('Record with given where clause not found');
        }

        return $result ?: null;
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function fetchBy(array $where = []): array
    {
        $entities = [];

        $queryBuilder = $this->connection->createQueryBuilder()
                ->select('*')
                ->from(self::TABLE)
                ->orderBy('date_created', 'DESC');

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->andWhere($column . ' = ' . $queryBuilder->createNamedParameter($value));
            }
        }

        $statement = $queryBuilder->executeQuery();

        $result = $statement->fetchAllAssociative();

        foreach ($result as $item) {
            $entities[] = $item;
        }

        return $entities;
    }

    /**
     * @throws Exception
     */
    public function countPictures(array $where = []): int
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

        $statement = $queryBuilder->executeQuery();

        return $statement->rowCount();
    }

    /**
     * Fetches a user by its id.
     *
     * @throws RecordNotFoundException
     * @throws Exception
     * @throws \Exception
     */
    public function fetchOneById(int $id): PicturesEntity
    {
        $statement = $this->connection->createQueryBuilder()
                ->select('*')
                ->from(self::TABLE)
                ->where('object_id = :id')
                ->setParameter('id', $id)
                ->executeQuery();

        $result = $statement->fetchAssociative();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException(
                    sprintf(
                            'Record with id #%s not found',
                            $id
                    )
            );
        }

        return $result ?: null;
    }

    /**
     * @throws RecordAlreadyExistsException
     * @throws Exception
     */

    /**
     * @throws Exception
     * @throws RecordNotPersistedException
     */

    /**
     * @throws RecordNotPersistedException
     * @throws Exception
     */


    /**
     * @throws \Exception
     */
}
