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
        $entity->rc = $data['rc'];
        $entity->ufi = $data['ufi'];
        $entity->uni = $data['uni'];
        $entity->lat = $data['lat'];
        $entity->lon = $data['lon'];
        $entity->dmsLat = $data['dms_lat'];
        $entity->dmsLon = $data['dms_lon'];
        $entity->utm = $data['utm'];
        $entity->jog = $data['jog'];
        $entity->fc = $data['fc'];
        $entity->dsg = $data['dsg'];
        $entity->pc = $data['pc'];
        $entity->cc1 = $data['cc1'];
        $entity->adm1 = $data['adm1'];
        $entity->adm2 = $data['adm2'];
        $entity->dim = $data['dim'];
        $entity->cc2 = $data['cc2'];
        $entity->nt = $data['nt'];
        $entity->lc = $data['lc'];
        $entity->sHORTFORM = $data['SHORT_FORM'];
        $entity->gENERIC = $data['GENERIC'];
        $entity->sORTNAME = $data['SORT_NAME'];
        $entity->fULLNAME = $data['FULL_NAME'];
        $entity->fULLNAMEND = $data['FULL_NAME_ND'];
        $entity->mODDATE = $data['MOD_DATE'];
        $entity->admtxt1 = $data['admtxt1'];
        $entity->admtxt3 = $data['admtxt3'];
        $entity->admtxt4 = $data['admtxt4'];
        $entity->admtxt2 = $data['admtxt2'];

        return $entity;
    }
}
