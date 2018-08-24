<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class GnsLocationsRepository
{
    const TABLE = 'gns_locations';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return GnsLocationsEntity[]
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
     * @return GnsLocationsEntity
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
     * @return GnsLocationsEntity[]
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
     * @param GnsLocationsEntity $entity
     * @return GnsLocationsEntity
     */
    public function create(GnsLocationsEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->rc = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param GnsLocationsEntity $entity
     * @return GnsLocationsEntity
     */
    public function update(GnsLocationsEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['rc' => $entity->rc]
        );

        return $entity;
    }

    /**
     * @param GnsLocationsEntity $entity
     * @return GnsLocationsEntity
     */
    public function remove(GnsLocationsEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['rc' => $entity->rc]
        );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param GnsLocationsEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(GnsLocationsEntity $entity)
    {
        return [
            'rc' => $entity->rc,
            'ufi' => $entity->ufi,
            'uni' => $entity->uni,
            'lat' => $entity->lat,
            'lon' => $entity->lon,
            'dms_lat' => $entity->dmsLat,
            'dms_lon' => $entity->dmsLon,
            'utm' => $entity->utm,
            'jog' => $entity->jog,
            'fc' => $entity->fc,
            'dsg' => $entity->dsg,
            'pc' => $entity->pc,
            'cc1' => $entity->cc1,
            'adm1' => $entity->adm1,
            'adm2' => $entity->adm2,
            'dim' => $entity->dim,
            'cc2' => $entity->cc2,
            'nt' => $entity->nt,
            'lc' => $entity->lc,
            'SHORT_FORM' => $entity->sHORTFORM,
            'GENERIC' => $entity->gENERIC,
            'SORT_NAME' => $entity->sORTNAME,
            'FULL_NAME' => $entity->fULLNAME,
            'FULL_NAME_ND' => $entity->fULLNAMEND,
            'MOD_DATE' => $entity->mODDATE,
            'admtxt1' => $entity->admtxt1,
            'admtxt3' => $entity->admtxt3,
            'admtxt4' => $entity->admtxt4,
            'admtxt2' => $entity->admtxt2,
        ];
    }

    /**
     * @param array $data
     * @return GnsLocationsEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GnsLocationsEntity();
        $entity->rc = (int) $data['rc'];
        $entity->ufi = (int) $data['ufi'];
        $entity->uni = (int) $data['uni'];
        $entity->lat = $data['lat'];
        $entity->lon = $data['lon'];
        $entity->dmsLat = (int) $data['dms_lat'];
        $entity->dmsLon = (int) $data['dms_lon'];
        $entity->utm = (string) $data['utm'];
        $entity->jog = (string) $data['jog'];
        $entity->fc = (string) $data['fc'];
        $entity->dsg = (string) $data['dsg'];
        $entity->pc = (int) $data['pc'];
        $entity->cc1 = (string) $data['cc1'];
        $entity->adm1 = (string) $data['adm1'];
        $entity->adm2 = (string) $data['adm2'];
        $entity->dim = (int) $data['dim'];
        $entity->cc2 = (string) $data['cc2'];
        $entity->nt = (string) $data['nt'];
        $entity->lc = (string) $data['lc'];
        $entity->sHORTFORM = (string) $data['SHORT_FORM'];
        $entity->gENERIC = (string) $data['GENERIC'];
        $entity->sORTNAME = (string) $data['SORT_NAME'];
        $entity->fULLNAME = (string) $data['FULL_NAME'];
        $entity->fULLNAMEND = (string) $data['FULL_NAME_ND'];
        $entity->mODDATE = new DateTime($data['MOD_DATE']);
        $entity->admtxt1 = (string) $data['admtxt1'];
        $entity->admtxt3 = (string) $data['admtxt3'];
        $entity->admtxt4 = (string) $data['admtxt4'];
        $entity->admtxt2 = (string) $data['admtxt2'];

        return $entity;
    }
}
