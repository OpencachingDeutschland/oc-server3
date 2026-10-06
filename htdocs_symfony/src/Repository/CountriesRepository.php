<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CountriesRepository
{
    private const TABLE = 'countries';

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws RecordsNotFoundException
     * @throws Exception
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
     */
    public function fetchOneBy(array $where = []): CountriesEntity
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
     * @throws RecordsNotFoundException
     * @throws Exception
     */
    public function fetchBy(array $where = []): array
    {
        $queryBuilder = $this->connection->createQueryBuilder()
                ->select('*')
                ->from(self::TABLE);

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->andWhere($column . ' = ' . $queryBuilder->createNamedParameter($value));
            }
        }

        $statement = $queryBuilder->executeQuery();

        $result = $statement->fetchAllAssociative();

        if ($statement->rowCount() === 0) {
            throw new RecordsNotFoundException('No records with given where clause found');
        }

        $entities = [];

        foreach ($result as $item) {
            $entities[] = $item;
        }

        return $entities;
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
     * @throws Exception
     * @throws RecordNotPersistedException
     * @throws InvalidArgumentException
     */

    /**
     * fetch all countries from DB, sort them ascending
     *
     * @throws RecordsNotFoundException
     * @throws Exception
     */
    public function fetchCountryList(string $locale): array
    {
        $fetchedCountries = $this->fetchAll();
        $countryList = [];

        foreach ($fetchedCountries as $country) {
            if ($locale == 'de') {
                $countryList[$country->de] = $country->short;
            } else {
                $countryList[$country->en] = $country->short;
            }
        }

        ksort($countryList);

        return ($countryList);
    }



    /** @throws Exception */
    public function fetchLookupCountries(string $lang): array
    {
        return $this->connection->createQueryBuilder()
            ->select('c.short', 'IFNULL(stt.text, c.name) AS name')
            ->from('countries', 'c')
            ->leftJoin('c', 'sys_trans', 'st', 'c.trans_id = st.id')
            ->leftJoin('st', 'sys_trans_text', 'stt', 'st.id = stt.trans_id AND stt.lang = :lang')
            ->orderBy('name')
            ->setParameter('lang', $lang)
            ->executeQuery()
            ->fetchAllAssociative();
    }
}
