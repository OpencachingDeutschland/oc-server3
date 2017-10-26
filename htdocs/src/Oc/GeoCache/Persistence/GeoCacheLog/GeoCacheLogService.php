<?php

namespace Oc\GeoCache\Persistence\GeoCacheLog;

use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordsNotFoundException;

class GeoCacheLogService
{
    /**
     * @var GeoCacheLogRepository
     */
    private $geoCacheRepository;

    /**
     * @param GeoCacheLogRepository $geoCacheRepository
     */
    public function __construct(GeoCacheLogRepository $geoCacheRepository)
    {
        $this->geoCacheRepository = $geoCacheRepository;
    }

    /**
     * Fetches all GeoCacheLogs.
     *
     * @return GeoCacheLogEntity[]
     */
    public function fetchAll()
    {
        try {
            $result = $this->geoCacheRepository->fetchAll();
        } catch (RecordsNotFoundException $e) {
            $result = [];
        }

        return $result;
    }

    /**
     * Fetches the latest geo cache log for given user id.
     *
     * @param int $userId
     *
     * @return GeoCacheLogEntity|null
     */
    public function getLatestUserLog($userId)
    {
        try {
            $result = $this->geoCacheRepository->getLatestUserLog($userId);
        } catch (RecordNotFoundException $e) {
            $result = null;
        }

        return $result;
    }

    /**
     * Creates a GeoCacheLog in the database.
     *
     * @param GeoCacheLogEntity $entity
     *
     * @return GeoCacheLogEntity
     */
    public function create(GeoCacheLogEntity $entity)
    {
        return $this->geoCacheRepository->create($entity);
    }

    /**
     * Update a GeoCacheLog in the database.
     *
     * @param GeoCacheLogEntity $entity
     *
     * @return GeoCacheLogEntity
     */
    public function update(GeoCacheLogEntity $entity)
    {
        return $this->geoCacheRepository->update($entity);
    }

    /**
     * Removes a GeoCacheLog from the database.
     *
     * @param GeoCacheLogEntity $entity
     *
     * @return GeoCacheLogEntity
     */
    public function remove(GeoCacheLogEntity $entity)
    {
        return $this->geoCacheRepository->remove($entity);
    }
}
